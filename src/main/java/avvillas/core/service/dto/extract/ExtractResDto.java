package avvillas.core.service.dto.extract;

import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.time.LocalDateTime;

@Data
@NoArgsConstructor
@AllArgsConstructor
public class ExtractResDto {

    private String id;

    private String period;
    private String status;
    private int percentAdvance;
    private LocalDateTime date;
    private String details;

    private ProductCodeDescriptionDTO product;
    private UserNameAndEmailDto user;
}