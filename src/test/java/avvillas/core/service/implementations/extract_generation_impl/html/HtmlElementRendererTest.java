package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.extract_generation.BarcodeService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties.Process;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;

import java.util.Map;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.Mockito.when;
import static org.mockito.Mockito.mock;

class HtmlElementRendererTest {

    private BarcodeService barcodeService;
    private HtmlElementRenderer renderer;

    @BeforeEach
    void setUp() {
        barcodeService = mock(BarcodeService.class);
        ExtractProperties extractProperties = mock(ExtractProperties.class);

        Process process = new Process();
        process.setBarcodeWidth(String.valueOf(100));
        process.setBarcodeHeight(String.valueOf(50));

        when(extractProperties.getProcess()).thenReturn(process);

        renderer = new HtmlElementRenderer(barcodeService, extractProperties);
    }

    @Test
    void generateForPage_shouldReturnEmpty_whenElementsNull() {
        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(null);

        String result = renderer.generateForPage(Map.of(), page, null, List.of());

        assertThat(result).isEmpty();
    }

    @Test
    void generateForPage_shouldRenderSimpleTextElement() {
        FormatDto.ElementResponse element = new FormatDto.ElementResponse();
        element.setFieldId("name");
        element.setPositionX(10);
        element.setPositionY(20);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        Map<String, Object> clientData = Map.of("name", "John Doe");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).contains("John Doe");
        assertThat(result).contains("left: 10px");
        assertThat(result).contains("top: 20px");
    }

    @Test
    void generateForPage_shouldRenderMoneyElement() {
        FormatDto.ElementResponse element = new FormatDto.ElementResponse();
        element.setFieldId("balance");
        element.setPositionX(5);
        element.setPositionY(15);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        Map<String, Object> clientData = Map.of("balance", "MONEY::12345");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).contains("12345");
        assertThat(result).contains("text-align: right;");
        assertThat(result).contains("width: 75px;");
    }

    @Test
    void generateForPage_shouldRenderBarcodeElement() throws Exception {
        FormatDto.ElementResponse element = new FormatDto.ElementResponse();
        element.setFieldId("barcode");
        element.setPositionX(30);
        element.setPositionY(40);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        when(barcodeService.generateCode128BarcodeAsBase64("123456")).thenReturn("data:image/png;base64,xxx");

        Map<String, Object> clientData = Map.of("barcode", "BARCODE::123456");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).contains("img src=\"data:image/png;base64,xxx\"");
        assertThat(result).contains("123456");
        assertThat(result).contains("width: 100px;");
        assertThat(result).contains("height: 50px;");
    }

    @Test
    void generateForPage_shouldSkipBarcode_whenServiceReturnsNull() throws Exception {
        FormatDto.ElementResponse element = new FormatDto.ElementResponse();
        element.setFieldId("barcode");
        element.setPositionX(0);
        element.setPositionY(0);

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of(element));

        when(barcodeService.generateCode128BarcodeAsBase64("999")).thenReturn(null);

        Map<String, Object> clientData = Map.of("barcode", "BARCODE::999");

        String result = renderer.generateForPage(clientData, page, null, List.of());

        assertThat(result).doesNotContain("img");
    }

    @Test
    void generateForPage_shouldRenderArrayRows() {
        FormatDto.ElementResponse col1 = new FormatDto.ElementResponse();
        col1.setFieldId("item");
        col1.setPositionX(10);
        col1.setPositionY(100);

        FormatDto.ElementResponse col2 = new FormatDto.ElementResponse();
        col2.setFieldId("price");
        col2.setPositionX(200);
        col2.setPositionY(100);

        List<FormatDto.ElementResponse> arrayGroup = List.of(col1, col2);

        List<Map<String, String>> rows = List.of(
                Map.of("item", "Book", "price", "MONEY::50"),
                Map.of("item", "Pen", "price", "MONEY::5")
        );

        FormatDto.PageResponse page = new FormatDto.PageResponse();
        page.setElements(List.of());

        String result = renderer.generateForPage(Map.of(), page, rows, arrayGroup);

        assertThat(result).contains("Book");
        assertThat(result).contains("50");
        assertThat(result).contains("Pen");
        assertThat(result).contains("5");

        assertThat(result).contains("top: 100px;");
        assertThat(result).contains("top: 118px;");
    }
}