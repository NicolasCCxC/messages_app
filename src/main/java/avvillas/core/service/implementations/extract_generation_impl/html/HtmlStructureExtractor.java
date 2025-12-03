package avvillas.core.service.implementations.extract_generation_impl.html;

import org.springframework.stereotype.Component;

@Component
public class HtmlStructureExtractor {

    private static final String PAGE_DIV_CLASS = "<div class='page'>";
    private static final String CLOSE_DIV_TAG = "</div>";
    private static final String DIV_ELEMENT = "<div";

    public HtmlPageStructure extract(String baseHtmlTemplate) {
        int firstPageStart = baseHtmlTemplate.indexOf(PAGE_DIV_CLASS);
        if (firstPageStart == -1) {
            return new HtmlPageStructure("", baseHtmlTemplate, "", "");
        }

        int firstPageEnd = findMatchingCloseDiv(baseHtmlTemplate, firstPageStart);
        if (firstPageEnd == -1) {
            String head = baseHtmlTemplate.substring(0, firstPageStart);
            String page1 = baseHtmlTemplate.substring(firstPageStart);
            return new HtmlPageStructure(head, page1, "", "");
        }

        String htmlHead = baseHtmlTemplate.substring(0, firstPageStart);
        String firstPage = baseHtmlTemplate.substring(firstPageStart, firstPageEnd);

        int secondPageStart = baseHtmlTemplate.indexOf(PAGE_DIV_CLASS, firstPageEnd);
        String secondPage = "";
        String htmlFoot = "";

        if (secondPageStart != -1) {
            int secondPageEnd = findMatchingCloseDiv(baseHtmlTemplate, secondPageStart);
            if (secondPageEnd != -1) {
                secondPage = baseHtmlTemplate.substring(secondPageStart, secondPageEnd);
                htmlFoot = baseHtmlTemplate.substring(secondPageEnd);
            } else {
                secondPage = baseHtmlTemplate.substring(secondPageStart);
            }
        } else {
            htmlFoot = baseHtmlTemplate.substring(firstPageEnd);
        }
        return new HtmlPageStructure(htmlHead, firstPage, secondPage, htmlFoot);
    }

    private int findMatchingCloseDiv(String html, int openDivStart) {
        int divCount = 1;
        int currentPos = html.indexOf('>', openDivStart) + 1;

        while (currentPos < html.length() && divCount > 0) {
            int nextOpenDiv = html.indexOf(DIV_ELEMENT, currentPos);
            int nextCloseDiv = html.indexOf(CLOSE_DIV_TAG, currentPos);

            if (nextCloseDiv == -1) return -1;

            if (nextOpenDiv != -1 && nextOpenDiv < nextCloseDiv) {
                divCount++;
                currentPos = nextOpenDiv + 4;
            } else {
                divCount--;
                currentPos = nextCloseDiv + 6;
                if (divCount == 0) return currentPos;
            }
        }
        return -1;
    }

    public record HtmlPageStructure(String htmlHead, String firstPageTemplate, String continuationPageTemplate,
                                    String htmlFoot) {
    }
}
