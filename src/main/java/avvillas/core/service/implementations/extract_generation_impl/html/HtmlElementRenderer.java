package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.extract_generation.BarcodeService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Component;

import java.io.IOException;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.stream.Collectors;

@Component
@RequiredArgsConstructor
public class HtmlElementRenderer {

    private static final Logger logger = LoggerFactory.getLogger(HtmlElementRenderer.class);

    private static final int LINE_HEIGHT = 18;
    private static final String BARCODE_PREFIX = "BARCODE::";
    private static final String MONEY_PREFIX = "MONEY::";

    private final BarcodeService barcodeService;
    private final ExtractProperties extractProperties;

    private String getBaseElementStyle() {
        String fontSize = extractProperties.getProcess().getStructureFieldFontSize();
        return String.format("font-weight: 100 !important; font-size: %s !important; position: absolute;", fontSize);
    }

    private String createMoneyElement(String prefixedValue, int x, int y) {
        String content = prefixedValue.substring(MONEY_PREFIX.length());
        int columnWidth = 75;
        return String.format(
                "<div style=\"%s left: %dpx; top: %dpx; width: %dpx; text-align: right;\">%s</div>",
                getBaseElementStyle(), x, y, columnWidth, content
        );
    }

    public String generateForPage(Map<String, Object> clientData,
                                  FormatDto.PageResponse pageConfig,
                                  List<Map<String, String>> rowsForThisPage,
                                  List<FormatDto.ElementResponse> arrayColumnGroup) {

        if (pageConfig.getElements() == null) return "";

        StringBuilder elementsHtml = new StringBuilder();
        Set<String> arrayFieldIds = arrayColumnGroup.stream()
                .map(FormatDto.ElementResponse::getFieldId)
                .collect(Collectors.toSet());

        pageConfig.getElements().stream()
                .filter(element -> !arrayFieldIds.contains(element.getFieldId()))
                .forEach(element -> {
                    String value = clientData.getOrDefault(element.getFieldId(), "").toString();
                    if (!value.isEmpty()) {
                        if (value.startsWith(BARCODE_PREFIX)) {
                            elementsHtml.append(createBarcodeElement(value, element));
                        } else if (value.startsWith(MONEY_PREFIX)) {
                            elementsHtml.append(createMoneyElement(value, element.getPositionX(), element.getPositionY()));
                        } else {
                            elementsHtml.append(createPositionedElement(value, element.getPositionX(), element.getPositionY()));
                        }
                    }
                });

        if (rowsForThisPage != null) {
            for (int i = 0; i < rowsForThisPage.size(); i++) {
                Map<String, String> currentRow = rowsForThisPage.get(i);
                int baseY = i * LINE_HEIGHT;
                for (FormatDto.ElementResponse columnConfig : arrayColumnGroup) {
                    String cellValue = currentRow.getOrDefault(columnConfig.getFieldId(), "").trim();
                    if (!cellValue.isEmpty()) {
                        int finalX = columnConfig.getPositionX();
                        int finalY = columnConfig.getPositionY() + baseY;

                        if (cellValue.startsWith(MONEY_PREFIX)) {
                            elementsHtml.append(createMoneyElement(cellValue, finalX, finalY));
                        } else {
                            elementsHtml.append(createPositionedElement(cellValue, finalX, finalY));
                        }
                    }
                }
            }
        }
        return elementsHtml.toString();
    }

    private String createPositionedElement(String content, int x, int y) {
        return "<span style=\"" + getBaseElementStyle() +
                " left: " + x + "px; top: " + y + "px;\">" +
                content + "</span>";
    }

    private String createBarcodeElement(String prefixedValue, FormatDto.ElementResponse element) {
        String barcodeValue = prefixedValue.substring(BARCODE_PREFIX.length());

        try {
            String base64Image = barcodeService.generateCode128BarcodeAsBase64(barcodeValue);
            if (base64Image == null) return "";

            return String.format(
                    "<div style=\"position: absolute; top: %dpx; left: %dpx;\">" +
                            "<img src=\"%s\" alt=\"Barcode\" style=\"display: block; width: %spx; height: %spx; margin: 0 auto;\" />" +
                            "<div style=\"text-align: center; font-size: 5px; margin-bottom: 0px;\">%s</div>" +
                            "</div>",

                    element.getPositionY(),
                    element.getPositionX(),
                    base64Image,
                    extractProperties.getProcess().getBarcodeWidth(),
                    extractProperties.getProcess().getBarcodeHeight(),
                    barcodeValue
            );

        } catch (IOException e) {
            logger.error("Error generando HTML para código de barras con valor '{}'", barcodeValue, e);
            return "";
        }
    }
}
