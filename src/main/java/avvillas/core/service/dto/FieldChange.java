package avvillas.core.service.dto;

import lombok.AllArgsConstructor;
import lombok.Data;

@Data
@AllArgsConstructor
@SuppressWarnings("unused")
public class FieldChange {
    private String fieldName;
    private Object previousValue;
    private Object newValue;
}
