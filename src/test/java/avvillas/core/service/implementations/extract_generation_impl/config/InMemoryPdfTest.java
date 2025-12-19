package avvillas.core.service.implementations.extract_generation_impl.config;

import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import static org.assertj.core.api.Assertions.assertThat;

class InMemoryPdfTest {

    @Test
    @DisplayName("Verifica que el constructor, getters, equals, hashCode y toString funcionen")
    void testRecordMethods() {
        String fileName = "test.pdf";
        byte[] content = new byte[]{1, 2, 3};

        InMemoryPdf pdf1 = new InMemoryPdf(fileName, content);

        assertThat(pdf1.fileName()).isEqualTo(fileName);
        assertThat(pdf1.content()).isEqualTo(content);

        InMemoryPdf pdf2 = new InMemoryPdf(fileName, content);

        assertThat(pdf1).isEqualTo(pdf2);

        assertThat(pdf1.hashCode()).isEqualTo(pdf2.hashCode());

        InMemoryPdf pdfThreeDiffNa = new InMemoryPdf("otro.pdf", content);
        InMemoryPdf pdfFOurDiffContent = new InMemoryPdf(fileName, new byte[]{4, 5, 6});

        assertThat(pdf1).isNotEqualTo(pdfThreeDiffNa);
        assertThat(pdf1).isNotEqualTo(pdfFOurDiffContent);

        assertThat(pdf1.toString()).contains(fileName);
    }
}