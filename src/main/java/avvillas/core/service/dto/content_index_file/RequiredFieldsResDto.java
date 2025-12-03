package avvillas.core.service.dto.content_index_file;

import lombok.Data;

@Data
public class RequiredFieldsResDto {
    private String id;
    private Boolean isFixed;
    private String content;
    private InputProductStructureFieldNameResDto inputStructureProduct;
}