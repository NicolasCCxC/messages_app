package avvillas.core.service.dto.content_index_file;

import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import lombok.Data;

import java.util.List;

@Data
public class ContentIndexFileDto {
    private String id;
    private ProductCodeDescriptionDTO product;
    private TypeFile typeFile;
    private List<RequiredFieldsResDto> requiredFields;
    private String nameIndexFile;

    public enum TypeFile {
        CSV,
        TXT
    }
}