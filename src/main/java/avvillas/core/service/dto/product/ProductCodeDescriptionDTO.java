package avvillas.core.service.dto.product;

import lombok.AllArgsConstructor;
import lombok.Data;

@Data
@AllArgsConstructor
public class ProductCodeDescriptionDTO {
    private String id;
    private String code;
    private String description;
}