package avvillas.core.service.dto.path_index_file;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.time.LocalDateTime;

@Data
@NoArgsConstructor
@AllArgsConstructor
public class PathExtractsArchiveIndexDto {
    private String id;

    private String productId;

    private String routeOutputExtract;

    private String routeOutputIndex;

    private LocalDateTime createdAt;
    private LocalDateTime updateAt;
}
