package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.implementations.extract_generation_impl.html.ArrayLayoutAnalyzer;
import avvillas.core.service.implementations.extract_generation_impl.html.HtmlElementRenderer;
import avvillas.core.service.implementations.extract_generation_impl.html.HtmlStructureExtractor;
import avvillas.core.service.implementations.extract_generation_impl.html.RowDataDistributor;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;

import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.assertFalse;
import static org.junit.jupiter.api.Assertions.assertTrue;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.ArgumentMatchers.isNull;
import static org.mockito.Mockito.never;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@MockitoSettings(strictness = Strictness.LENIENT)
class HtmlTemplateServiceImplTest extends BaseServiceTest {

    @Mock
    private HtmlStructureExtractor structureExtractor;
    @Mock
    private ArrayLayoutAnalyzer layoutAnalyzer;
    @Mock
    private RowDataDistributor rowDataDistributor;
    @Mock
    private HtmlElementRenderer elementRenderer;

    @InjectMocks
    private HtmlTemplateServiceImpl htmlTemplateService;

    private FormatDto mockFormat;
    private Map<String, Object> clientData;
    // Eliminado mockStructure de aquí

    @BeforeEach
    void setUp() {
        clientData = new HashMap<>();
        clientData.put("name", "John Doe");
        clientData.put("city", "Bogota");
        clientData.put("listField", List.of("item1", "item2"));
        clientData.put("nullField", null);

        mockFormat = buildMockFormat();

        // Eliminado when(structureExtractor.extract...) de setUp()
    }

    private FormatDto buildMockFormat() {
        FormatDto format = new FormatDto();
        FormatDto.PdfConfigResponse pdfConfig = new FormatDto.PdfConfigResponse();
        pdfConfig.setContinues(true);
        format.setPdfConfig(pdfConfig);

        FormatDto.PageResponse page1 = new FormatDto.PageResponse();
        page1.setPageNumber(1);
        page1.setElements(new ArrayList<>());

        FormatDto.PageResponse page2 = new FormatDto.PageResponse();
        page2.setPageNumber(2);
        page2.setElements(new ArrayList<>());

        format.setPages(List.of(page1, page2));
        return format;
    }

    @Test
    @DisplayName("Verifica el reemplazo de placeholders y la ruta estática (sin array)")
    void prepareClientHtml_shouldReplacePlaceholdersAndRenderStaticPath() {
        String baseHtml = "<html><head><div class='page'>Contenido P1 {{name}} {{city}} {{listField}} {{nullField}}</div></head></html>";
        String staticElementsHtml = "<p>Elementos Estaticos</p>";
        
        String expectedHtmlAfterReplace = "<html><head><div class='page'>Contenido P1 John Doe Bogota {{listField}} </div></head></html>";

        HtmlStructureExtractor.HtmlPageStructure parsedStructure = new HtmlStructureExtractor.HtmlPageStructure(
                "<html><head>",
                "<div class='page'>Contenido P1 John Doe Bogota {{listField}} </div>",
                "",
                "</head></html>"
        );

        when(structureExtractor.extract(eq(expectedHtmlAfterReplace))).thenReturn(parsedStructure);


        when(layoutAnalyzer.findArrayColumnGroup(eq(mockFormat.getPages().getFirst()), eq(clientData)))
                .thenReturn(Collections.emptyList());

        when(elementRenderer.generateForPage(eq(clientData), eq(mockFormat.getPages().getFirst()), isNull(), any()))
                .thenReturn(staticElementsHtml);

        String result = htmlTemplateService.prepareClientHtml(baseHtml, clientData, mockFormat);

        verify(rowDataDistributor, never()).calculate(any(), any(), any());

        assertTrue(result.contains(parsedStructure.htmlHead()));
        assertTrue(result.contains("John Doe"));
        assertTrue(result.contains("Bogota"));
        assertTrue(result.contains(staticElementsHtml));
        assertTrue(result.contains(parsedStructure.htmlFoot()));

        assertFalse(result.contains("{{name}}"));
        assertFalse(result.contains("{{nullField}}"));
        assertTrue(result.contains("{{listField}}"));
    }
}