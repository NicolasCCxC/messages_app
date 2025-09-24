package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.service.extract_generation.BarcodeService;
import com.google.zxing.BarcodeFormat;
import com.google.zxing.EncodeHintType;
import com.google.zxing.client.j2se.MatrixToImageWriter;
import com.google.zxing.common.BitMatrix;
import com.google.zxing.oned.Code128Writer;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;

import javax.imageio.ImageIO;
import java.awt.image.BufferedImage;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.util.Base64;
import java.util.EnumMap;
import java.util.Map;

@Service
@RequiredArgsConstructor
public class BarcodeServiceImpl implements BarcodeService {

    @Override
    public String generateCode128BarcodeAsBase64(String barcodeText) throws IOException {
        if (barcodeText == null || barcodeText.isEmpty()) {
            return null;
        }
        
        try {
            Code128Writer barcodeWriter = new Code128Writer();
            Map<EncodeHintType, Object> hints = new EnumMap<>(EncodeHintType.class);
            hints.put(EncodeHintType.MARGIN, 0);

            BitMatrix bitMatrix = barcodeWriter.encode(
                    barcodeText,
                    BarcodeFormat.CODE_128,
                    400,
                    60,
                    hints
            );

            BufferedImage barcodeImage = MatrixToImageWriter.toBufferedImage(bitMatrix);

            ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
            ImageIO.write(barcodeImage, "png", outputStream);
            byte[] imageBytes = outputStream.toByteArray();

            return "data:image/png;base64," + Base64.getEncoder().encodeToString(imageBytes);
        } catch (Exception e) {
            throw new IOException("Fallo al generar el código de barras para el texto: " + barcodeText, e);
        }
    }
}