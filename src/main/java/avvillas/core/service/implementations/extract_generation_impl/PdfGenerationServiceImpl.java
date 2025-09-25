package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.service.extract_generation.PdfGenerationService;
import com.openhtmltopdf.pdfboxout.PdfRendererBuilder;
import lombok.SneakyThrows;
import org.apache.pdfbox.pdmodel.PDDocument;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.util.Map;

@Service
public class PdfGenerationServiceImpl implements PdfGenerationService {

    private static final Logger logger = LoggerFactory.getLogger(PdfGenerationServiceImpl.class);
    private static byte[] fontBytes;
    private static final ThreadLocal<PdfRendererBuilder> rendererBuilderThreadLocal = ThreadLocal.withInitial(() -> {
        PdfRendererBuilder builder = new PdfRendererBuilder();
        builder.useFastMode();
        if (fontBytes != null) {
            builder.useFont(() -> new ByteArrayInputStream(fontBytes), "Arial");
        }
        return builder;
    });

    @SneakyThrows
    public PdfGenerationServiceImpl(byte[] fontBytes) {
        if (PdfGenerationServiceImpl.fontBytes == null) {
            PdfGenerationServiceImpl.fontBytes = fontBytes;
        }
    }

    public static void cleanupThread() {
        rendererBuilderThreadLocal.remove();
    }

    @Override
    public byte[] generatePdf(Map<String, Object> clientData, String finalHtml) {
        String fileName = clientData.getOrDefault("fileName", "extract-" + System.currentTimeMillis()).toString();
        PdfRendererBuilder builder = rendererBuilderThreadLocal.get();

        try (ByteArrayOutputStream os = new ByteArrayOutputStream(100 * 1024);
             PDDocument document = new PDDocument()) {

            builder.usePDDocument(document);
            builder.withHtmlContent(finalHtml, null);
            builder.toStream(os);
            builder.run();

            return os.toByteArray();

        } catch (Exception e) {
            logger.error("Fallo al generar PDF en memoria para el cliente [{}]: {}", fileName, e.getMessage());
            return new byte[0];
        }
    }

}