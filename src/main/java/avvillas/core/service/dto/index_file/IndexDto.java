package avvillas.core.service.dto.index_file;

import avvillas.core.constant.message.IndexFileMessage;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import jakarta.validation.constraints.Size;
import lombok.Data;

import java.time.LocalDateTime;

@Data
public class IndexDto {
    @NotBlank(message = IndexFileMessage.PRODUCT_ID_REQUIRED)
    @Pattern(
            regexp = "^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$",
            message = IndexFileMessage.PRODUCT_ID_INVALID
    )
    private String productId;

    @NotBlank(message = IndexFileMessage.PERIOD_REQUIRED)
    @Size(min = 8, max = 8, message = IndexFileMessage.PERIOD_MAX)
    private String period;

    private String status;

    private String user;

    private Integer percentAdvance;

    private Integer clientsProcessed;

    private LocalDateTime createdAt;
}
