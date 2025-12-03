package avvillas.core.service.dto.product;

import lombok.Data;

import java.time.LocalDateTime;

@Data
public class ProductDTO {
    private String id;
    private String code;
    private String description;
    private String documentType;
    private Boolean active;
    private LocalDateTime createdAt;
    private LocalDateTime updateAt;
}