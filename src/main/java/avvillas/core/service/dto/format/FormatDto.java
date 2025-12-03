package avvillas.core.service.dto.format;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.List;

@Data
@NoArgsConstructor
@AllArgsConstructor
public class FormatDto {

    private String id;
    private String productId;
    private Boolean active;
    private Integer version;
    private String htmlContent;
    private String base64Content;
    private PdfConfigResponse pdfConfig;
    private List<PageResponse> pages;

    @Data
    public static class PdfConfigResponse {
        private Boolean continues;
        private String paperType;
        private String fontFamily;
        private Margins margins;
    }

    @Data
    public static class Margins {
        private int top;
        private int left;
        private int right;
        private int bottom;
    }

    @Data
    public static class PageResponse {
        private int pageNumber;
        private List<ElementResponse> elements;
    }

    @Data
    public static class ElementResponse {
        private String fieldId;
        private String objectId;
        private int positionX;
        private int positionY;
    }
}