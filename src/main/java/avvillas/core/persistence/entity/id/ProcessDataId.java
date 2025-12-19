package avvillas.core.persistence.entity.id;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.io.Serializable;

@Data
@NoArgsConstructor
@AllArgsConstructor
@SuppressWarnings("unused")
public class ProcessDataId implements Serializable {
    private String processId;
    private String clientId;
}
