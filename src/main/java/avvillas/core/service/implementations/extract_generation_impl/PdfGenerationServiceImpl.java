package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.service.extract_generation.PdfGenerationService;
import com.openhtmltopdf.pdfboxout.PdfRendererBuilder;
import lombok.SneakyThrows;
import org.apache.pdfbox.pdmodel.PDDocument;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.core.io.ClassPathResource;
import org.springframework.stereotype.Service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Map;

@Service
public class PdfGenerationServiceImpl implements PdfGenerationService {

    private static final Logger logger = LoggerFactory.getLogger(PdfGenerationServiceImpl.class);
    private static byte[] fontBytes;

    public PdfGenerationServiceImpl() {
        // Lazy-load font bytes from classpath if available
        if (fontBytes == null) {
            try (InputStream is = new ClassPathResource("fonts/arial.ttf").getInputStream();
                 ByteArrayOutputStream baos = new ByteArrayOutputStream()) {
                if (is != null) {
                    is.transferTo(baos);
                    fontBytes = baos.toByteArray();
                }
            } catch (IOException e) {
                // Font is optional; log and continue without custom font
                logger.warn("No se pudo cargar la fuente desde classpath: {}", e.getMessage());
            }
        }
    }

    public static void cleanupThread() {
        // No-op. Mantener por compatibilidad; ya no usamos ThreadLocal del builder.
    }

    @Override
    public byte[] generatePdf(Map<String, Object> clientData, String finalHtml) {
        String fileName = clientData.getOrDefault("fileName", "extract-" + System.currentTimeMillis()).toString();
        PdfRendererBuilder builder = new PdfRendererBuilder();
        builder.useFastMode();
        if (fontBytes != null) {
            builder.useFont(() -> new ByteArrayInputStream(fontBytes), "Arial");
        }

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