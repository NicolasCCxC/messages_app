package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.common.TestDataFactory;
import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.extract_generation.BarcodeService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;

import java.io.IOException;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.when;

@MockitoSettings(strictness = Strictness.LENIENT)
class HtmlElementRendererTest extends BaseServiceTest {

    @Mock
    private BarcodeService barcodeService;
    @Mock
    private ExtractProperties extractProperties;
    @Mock
    private ExtractProperties.Process processProperties;

    @InjectMocks
    private HtmlElementRenderer renderer;

    @BeforeEach
    void setUp() {
        when(extractProperties.getProcess()).thenReturn(processProperties);
        when(processProperties.getBarcodeWidth()).thenReturn("100");
        when(processProperties.getBarcodeHeight()).thenReturn("50");
    }

    @Test
    @DisplayName("Verifica que retorne vacío si la lista de elementos es nula")
    void generateForPage_shouldReturnEmptyWhenElementsAreNull() {
        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(null);

        String result = renderer.generateForPage(Map.of(), page, null, List.of());

        assertThat(result).isEmpty();
    }

    @Test
    @DisplayName("Verifica el renderizado de un elemento de texto estático")
    void generateForPage_shouldRenderStaticTextElement() {
        FormatDto.ElementResponse element = TestDataFactory.mockElementResponse("name", 20);
        element.setPositionX(10);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));
        Map<String, Object> clientData = Map.of("name", "John Doe");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result)
                .contains("John Doe")
                .contains("left: 10px")
                .contains("top: 20px")
                .startsWith("<span")
                .endsWith("</span>");
    }

    @Test
    @DisplayName("Verifica el renderizado de un elemento de dinero estático")
    void generateForPage_shouldRenderStaticMoneyElement() {
        FormatDto.ElementResponse element = TestDataFactory.mockElementResponse("balance", 15);
        element.setPositionX(5);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));
        Map<String, Object> clientData = Map.of("balance", "MONEY::12345");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result)
                .contains("12345")
                .contains("text-align: right;")
                .contains("width: 75px;");
    }

    @Test
    @DisplayName("Verifica el renderizado de un elemento de código de barras estático")
    void generateForPage_shouldRenderStaticBarcodeElement() throws Exception {
        FormatDto.ElementResponse element = TestDataFactory.mockElementResponse("barcode", 40);
        element.setPositionX(30);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        when(barcodeService.generateCode128BarcodeAsBase64("123456")).thenReturn("data:image/png;base64,xxx");
        Map<String, Object> clientData = Map.of("barcode", "BARCODE::123456");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result)
                .contains("img src=\"data:image/png;base64,xxx\"")
                .contains("123456")
                .contains("width: 100px;")
                .contains("height: 50px;");
    }

    @Test
    @DisplayName("Verifica que omita el código de barras si el servicio retorna nulo")
    void generateForPage_shouldSkipStaticBarcodeWhenServiceReturnsNull() throws Exception {
        FormatDto.ElementResponse element = TestDataFactory.mockElementResponse("barcode", 0);
        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        when(barcodeService.generateCode128BarcodeAsBase64("999")).thenReturn(null);
        Map<String, Object> clientData = Map.of("barcode", "BARCODE::999");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).isEmpty();
    }

    @Test
    @DisplayName("Verifica que omita el código de barras si el servicio lanza IOException")
    void generateForPage_shouldReturnEmptyBarcodeStringWhenServiceThrowsIOException() throws Exception {
        FormatDto.ElementResponse element = TestDataFactory.mockElementResponse("barcode", 0);
        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        when(barcodeService.generateCode128BarcodeAsBase64(anyString())).thenThrow(new IOException("Test Error"));
        Map<String, Object> clientData = Map.of("barcode", "BARCODE::999");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).isEmpty();
    }

    @Test
    @DisplayName("Verifica que omita elementos estáticos con valor vacío")
    void generateForPage_shouldSkipStaticElementIfValueIsEmpty() {
        FormatDto.ElementResponse element = TestDataFactory.mockElementResponse("name", 20);
        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));
        Map<String, Object> clientData = Map.of("name", "");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).isEmpty();
    }

    @Test
    @DisplayName("Verifica que los elementos estáticos ignoren los IDs del grupo de array")
    void generateForPage_shouldFilterArrayElementsFromStaticRendering() {
        FormatDto.ElementResponse staticEl = TestDataFactory.mockElementResponse("title", 10);
        FormatDto.ElementResponse arrayEl = TestDataFactory.mockElementResponse("colA", 50);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(staticEl, arrayEl));

        List<FormatDto.ElementResponse> arrayGroup = List.of(arrayEl);

        Map<String, Object> clientData = Map.of("title", "Static Title", "colA", "Array Data");

        String result = renderer.generateForPage(clientData, page, null, arrayGroup);

        assertThat(result)
                .contains("Static Title")
                .doesNotContain("Array Data");
    }

    @Test
    @DisplayName("Verifica el renderizado de las filas de un array dinámico")
    void generateForPage_shouldRenderDynamicArrayRows() {
        FormatDto.ElementResponse col1 = TestDataFactory.mockElementResponse("item", 100);
        col1.setPositionX(10);
        FormatDto.ElementResponse col2 = TestDataFactory.mockElementResponse("price", 100);
        col2.setPositionX(200);

        List<FormatDto.ElementResponse> arrayGroup = List.of(col1, col2);

        List<Map<String, String>> rows = List.of(
                Map.of("item", "Book", "price", "MONEY::50"),
                Map.of("item", "Pen", "price", "MONEY::5"),
                Map.of("item", "Empty", "price", "")
        );

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(Collections.emptyList());

        String result = renderer.generateForPage(Map.of(), page, rows, arrayGroup);

        assertThat(result).contains("Book");
        assertThat(result).contains("50");
        assertThat(result).contains("Pen");
        assertThat(result).contains("5");

        assertThat(result).contains("Empty");

        assertThat(result).contains("top: 100px;");
        assertThat(result).contains("top: 118px;");
        assertThat(result).contains("top: 136px;");
    }
}