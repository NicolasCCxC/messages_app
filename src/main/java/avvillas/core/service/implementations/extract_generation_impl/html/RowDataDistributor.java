package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.service.dto.format.FormatDto;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Component;

import java.util.ArrayList;
import java.util.Comparator;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Component
@RequiredArgsConstructor
public class RowDataDistributor {

    private static final int LINE_HEIGHT = 18;
    private static final int PIXEL_TOLERANCE = 5;
    private static final int DEFAULT_PAGE_HEIGHT_A4 = 1123;
    private final ArrayLayoutAnalyzer layoutAnalyzer;

    public ArrayDistribution calculate(Map<String, Object> clientData, FormatDto format, List<FormatDto.ElementResponse> firstPageColumnGroup) {
        List<Map<String, String>> allRows = reconstructRowsFromColumns(clientData, firstPageColumnGroup);
        Map<Integer, List<Map<String, String>>> rowsByPage = new HashMap<>();
        String size = format.getPdfConfig().getPaperType();
        List<Map<String, String>> rowsOnFirstPage = new ArrayList<>();
        rowsByPage.put(1, rowsOnFirstPage);

        FormatDto.PageResponse firstPageConfig = format.getPages().getFirst();
        int startYOnFirstPage = firstPageColumnGroup.getFirst().getPositionY();
        int limitYOnFirstPage = calculatePageLimit(firstPageConfig, firstPageColumnGroup, size) - LINE_HEIGHT;

        for (Map<String, String> row : allRows) {
            if ((startYOnFirstPage + (rowsOnFirstPage.size() * LINE_HEIGHT)) <= limitYOnFirstPage) {
                rowsOnFirstPage.add(row);
            } else {
                break;
            }
        }

        List<Map<String, String>> remainingRows = allRows.subList(rowsOnFirstPage.size(), allRows.size());
        if (!remainingRows.isEmpty() && format.getPages().size() > 1) {
            paginateRemainingRows(remainingRows, rowsByPage, clientData, format);
        }

        return new ArrayDistribution(rowsByPage.keySet().size(), rowsByPage);
    }

    private void paginateRemainingRows(List<Map<String, String>> remainingRows, Map<Integer, List<Map<String, String>>> rowsByPage, Map<String, Object> clientData, FormatDto format) {
        FormatDto.PageResponse contPageConfig = format.getPages().get(1);
        String size = format.getPdfConfig().getPaperType();
        List<FormatDto.ElementResponse> contColumnGroup = layoutAnalyzer.findArrayColumnGroup(contPageConfig, clientData);

        if (contColumnGroup.isEmpty()) return;

        int currentPageNum = 2;
        int startYOnContPage = contColumnGroup.getFirst().getPositionY();
        int limitYOnContPage = calculatePageLimit(contPageConfig, contColumnGroup, size) - LINE_HEIGHT;
        int rowsPerContPage = Math.max(1, (limitYOnContPage - startYOnContPage) / LINE_HEIGHT);

        for (int i = 0; i < remainingRows.size(); i += rowsPerContPage) {
            int end = Math.min(i + rowsPerContPage, remainingRows.size());
            rowsByPage.put(currentPageNum++, remainingRows.subList(i, end));
        }
    }

    public List<Map<String, String>> reconstructRowsFromColumns(Map<String, Object> clientData, List<FormatDto.ElementResponse> columnGroup) {
        int maxRows = columnGroup.stream()
                .map(column -> clientData.get(column.getFieldId()))
                .filter(List.class::isInstance)
                .map(list -> ((List<?>) list).size())
                .max(Integer::compareTo)
                .orElse(0);

        List<Map<String, String>> rows = new ArrayList<>(maxRows);
        for (int i = 0; i < maxRows; i++) {
            Map<String, String> row = new HashMap<>(columnGroup.size());
            for (FormatDto.ElementResponse column : columnGroup) {
                Object columnData = clientData.get(column.getFieldId());
                String cellValue = "";
                if (columnData instanceof List<?> list && i < list.size() && list.get(i) != null) {
                    cellValue = list.get(i).toString();
                }
                row.put(column.getFieldId(), cellValue);
            }
            rows.add(row);
        }
        return rows;
    }

    private int calculatePageLimit(FormatDto.PageResponse page, List<FormatDto.ElementResponse> columnGroup, String size) {
        if (page.getElements() == null || columnGroup.isEmpty()) {
            return getPageHeight(size);
        }

        int maxArrayY = columnGroup.stream()
                .mapToInt(FormatDto.ElementResponse::getPositionY)
                .max()
                .orElse(0);

        return page.getElements().stream()
                .filter(element -> element.getPositionY() > (maxArrayY + PIXEL_TOLERANCE))
                .min(Comparator.comparing(FormatDto.ElementResponse::getPositionY))
                .map(FormatDto.ElementResponse::getPositionY)
                .orElse(getPageHeight(size));
    }

    private int getPageHeight(String paperType) {
        if (paperType == null) return DEFAULT_PAGE_HEIGHT_A4;
        return switch (paperType.toUpperCase()) {
            case "LETTER" -> 1056;
            case "LEGAL" -> 1344;
            default -> DEFAULT_PAGE_HEIGHT_A4;
        };
    }

    public record ArrayDistribution(int totalPagesNeeded, Map<Integer, List<Map<String, String>>> rowsByPage) {
    }
}
