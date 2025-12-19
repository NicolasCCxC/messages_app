package avvillas.core.service.implementations.extract_generation_impl.html;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;

class HtmlStructureExtractorTest {

    private HtmlStructureExtractor extractor;

    @BeforeEach
    void setUp() {
        extractor = new HtmlStructureExtractor();
    }

    @Test
    @DisplayName("Verifica una plantilla simple sin 'page' divs")
    void extract_shouldHandleTemplateWithNoPageDivs() {
        String html = "<html><body>Contenido</body></html>";
        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals("", result.htmlHead());
        assertEquals(html, result.firstPageTemplate());
        assertEquals("", result.continuationPageTemplate());
        assertEquals("", result.htmlFoot());
    }

    @Test
    @DisplayName("Verifica una plantilla con una sola página y estructura completa")
    void extract_shouldHandleOnePageTemplate() {
        String head = "<html><head></head><body>";
        String page1 = "<div class='page'>PAGINA 1</div>";
        String foot = "</body></html>";
        String html = head + page1 + foot;

        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals(head, result.htmlHead());
        assertEquals(page1, result.firstPageTemplate());
        assertEquals("", result.continuationPageTemplate());
        assertEquals(foot, result.htmlFoot());
    }

    @Test
    @DisplayName("Verifica una plantilla con dos páginas y estructura completa")
    void extract_shouldHandleTwoPageTemplate() {
        String head = "<html><head>";
        String page1 = "<div class='page'>PAGINA 1</div>";
        String page2 = "<div class='page'>PAGINA 2</div>";
        String foot = "</body></html>";
        String html = head + page1 + page2 + foot;

        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals(head, result.htmlHead());
        assertEquals(page1, result.firstPageTemplate());
        assertEquals(page2, result.continuationPageTemplate());
        assertEquals(foot, result.htmlFoot());
    }

    @Test
    @DisplayName("Verifica que maneje divs anidados dentro de una página")
    void extract_shouldHandleNestedDivsInsidePage() {
        String head = "<html>";
        String page1 = "<div class='page'>P1 <div><p>Div Anidado</p></div></div>";
        String foot = "</html>";
        String html = head + page1 + foot;

        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals(head, result.htmlHead());
        assertEquals(page1, result.firstPageTemplate());
        assertEquals("", result.continuationPageTemplate());
        assertEquals(foot, result.htmlFoot());
    }

    @Test
    @DisplayName("Verifica una plantilla donde la primera página no tiene cierre")
    void extract_shouldHandleUnclosedFirstPage() {
        String head = "<html>";
        String page1 = "<div class='page'>PAGINA 1 <div>sin cierre";
        String html = head + page1;

        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals(head, result.htmlHead());
        assertEquals(page1, result.firstPageTemplate());
        assertEquals("", result.continuationPageTemplate());
        assertEquals("", result.htmlFoot());
    }

    @Test
    @DisplayName("Verifica una plantilla con dos páginas donde la segunda no tiene cierre")
    void extract_shouldHandleUnclosedSecondPage() {
        String head = "<html>";
        String page1 = "<div class='page'>PAGINA 1</div>";
        String page2 = "<div class='page'>PAGINA 2 <div>sin cierre";
        String html = head + page1 + page2;

        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals(head, result.htmlHead());
        assertEquals(page1, result.firstPageTemplate());
        assertEquals(page2, result.continuationPageTemplate());
        assertEquals("", result.htmlFoot());
    }

    @Test
    @DisplayName("Verifica el manejo de una plantilla vacía")
    void extract_shouldHandleEmptyString() {
        String html = "";
        HtmlStructureExtractor.HtmlPageStructure result = extractor.extract(html);

        assertEquals("", result.htmlHead());
        assertEquals("", result.firstPageTemplate());
        assertEquals("", result.continuationPageTemplate());
        assertEquals("", result.htmlFoot());
    }
}