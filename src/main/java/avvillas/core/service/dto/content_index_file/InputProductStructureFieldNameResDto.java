package avvillas.core.service.dto.content_index_file;

import lombok.AllArgsConstructor;
import lombok.Data;

@Data
@AllArgsConstructor
public class InputProductStructureFieldNameResDto {
    private String id;
    private String fieldName;
    private String indexFileIdentifier;
}
