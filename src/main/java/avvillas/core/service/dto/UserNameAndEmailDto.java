package avvillas.core.service.dto;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Data
@AllArgsConstructor
@NoArgsConstructor
@SuppressWarnings("unused")
public class UserNameAndEmailDto {
    private String id;
    private String email;
    private String name;
}
