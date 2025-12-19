package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.common.BaseServiceTest;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.MockedStatic;

import javax.imageio.ImageIO;
import java.awt.image.BufferedImage;
import java.io.ByteArrayOutputStream;
import java.io.IOException;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.mockStatic;

class BarcodeServiceImplTest extends BaseServiceTest {

    private final BarcodeServiceImpl barcodeService = new BarcodeServiceImpl();

    @Test
    @DisplayName("Verifica la generación exitosa de un código de barras")
    void shouldGenerateBarcodeSuccessfully() throws IOException {
        String barcodeText = "123456789";

        String result = barcodeService.generateCode128BarcodeAsBase64(barcodeText);

        assertThat(result).startsWith("data:image/png;base64,");

        assertThat(result.length()).isGreaterThan(50);
    }

    @Test
    @DisplayName("Verifica que retorne nulo si el texto de entrada es nulo")
    void shouldReturnNullForNullInput() throws IOException {
        String result = barcodeService.generateCode128BarcodeAsBase64(null);
        assertThat(result).isNull();
    }

    @Test
    @DisplayName("Verifica que retorne nulo si el texto de entrada está vacío")
    void shouldReturnNullForEmptyInput() throws IOException {
        String result = barcodeService.generateCode128BarcodeAsBase64("");
        assertThat(result).isNull();
    }

    @Test
    @DisplayName("Verifica que lance IOException si falla la escritura de la imagen")
    void shouldThrowIOExceptionWhenImageIOWFails() {
        String barcodeText = "123456";
        IOException simulatedException = new IOException("Simulated write error");

        try (MockedStatic<ImageIO> imageIoMock = mockStatic(ImageIO.class)) {

            imageIoMock.when(() -> ImageIO.write(any(BufferedImage.class), eq("png"), any(ByteArrayOutputStream.class)))
                    .thenThrow(simulatedException);

            assertThatThrownBy(() -> {
                barcodeService.generateCode128BarcodeAsBase64(barcodeText);
            })
                    .isInstanceOf(IOException.class)
                    .hasMessage("Fallo al generar el código de barras para el texto: " + barcodeText)
                    .hasCause(simulatedException);
        }
    }
}