package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.service.dto.format.FormatDto;
import org.springframework.stereotype.Component;

import java.util.ArrayList;
import java.util.Comparator;
import java.util.List;
import java.util.Map;

@Component
public class ArrayLayoutAnalyzer {

    private static final int PIXEL_TOLERANCE = 5;

    public List<FormatDto.ElementResponse> findArrayColumnGroup(FormatDto.PageResponse pageConfig, Map<String, Object> clientData) {
        if (pageConfig.getElements() == null || pageConfig.getElements().isEmpty()) {
            return new ArrayList<>();
        }

        List<FormatDto.ElementResponse> candidates = pageConfig.getElements().stream()
                .filter(el -> el.getFieldId() != null && !el.getFieldId().isEmpty() && el.getObjectId() == null)
                .sorted(Comparator.comparingInt(FormatDto.ElementResponse::getPositionY))
                .toList();

        if (candidates.isEmpty()) return new ArrayList<>();

        List<List<FormatDto.ElementResponse>> potentialGroups = groupElementsByPosition(candidates);

        return potentialGroups.stream()
                .filter(group -> hasArrayData(group, clientData))
                .max(Comparator.comparingInt(List::size))
                .orElse(new ArrayList<>());
    }

    private List<List<FormatDto.ElementResponse>> groupElementsByPosition(List<FormatDto.ElementResponse> elements) {
        List<List<FormatDto.ElementResponse>> groups = new ArrayList<>();
        List<FormatDto.ElementResponse> currentGroup = new ArrayList<>();
        currentGroup.add(elements.get(0));

        for (int i = 1; i < elements.size(); i++) {
            FormatDto.ElementResponse current = elements.get(i);
            FormatDto.ElementResponse last = currentGroup.get(0);
            if (Math.abs(current.getPositionY() - last.getPositionY()) <= PIXEL_TOLERANCE) {
                currentGroup.add(current);
            } else {
                groups.add(new ArrayList<>(currentGroup));
                currentGroup.clear();
                currentGroup.add(current);
            }
        }
        groups.add(currentGroup);
        return groups;
    }

    private boolean hasArrayData(List<FormatDto.ElementResponse> group, Map<String, Object> clientData) {
        return group.stream().anyMatch(el -> clientData.get(el.getFieldId()) instanceof List);
    }
}
