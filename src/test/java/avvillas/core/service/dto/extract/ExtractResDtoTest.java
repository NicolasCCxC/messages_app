package avvillas.core.service.dto.extract;

import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.time.LocalDateTime;

import static org.assertj.core.api.Assertions.assertThat;

class ExtractResDtoTest {

    @Test
    @DisplayName("Verifica los getters, setters y el constructor sin argumentos")
    void testGettersAndSetters() {
        ExtractResDto dto = new ExtractResDto();

        LocalDateTime now = LocalDateTime.now();
        ProductCodeDescriptionDTO product = new ProductCodeDescriptionDTO("p1", "c1", "Prod 1");
        UserNameAndEmailDto user = new UserNameAndEmailDto("u1", "email", "User 1");

        dto.setId("id1");
        dto.setPeriod("202501");
        dto.setStatus("ACTIVO");
        dto.setPercentAdvance(50);
        dto.setDate(now);
        dto.setDetails("Detalles");
        dto.setProduct(product);
        dto.setUser(user);

        assertThat(dto.getId()).isEqualTo("id1");
        assertThat(dto.getPeriod()).isEqualTo("202501");
        assertThat(dto.getStatus()).isEqualTo("ACTIVO");
        assertThat(dto.getPercentAdvance()).isEqualTo(50);
        assertThat(dto.getDate()).isEqualTo(now);
        assertThat(dto.getDetails()).isEqualTo("Detalles");
        assertThat(dto.getProduct()).isEqualTo(product);
        assertThat(dto.getUser()).isEqualTo(user);
    }

    @Test
    @DisplayName("Verifica el constructor con todos los argumentos")
    void testAllArgsConstructor() {
        LocalDateTime now = LocalDateTime.now();
        ProductCodeDescriptionDTO product = new ProductCodeDescriptionDTO("p1", "c1", "Prod 1");
        UserNameAndEmailDto user = new UserNameAndEmailDto("u1", "email", "User 1");

        ExtractResDto dto = new ExtractResDto(
                "id1", "202501", "ACTIVO", 50, now, "Detalles", product, user
        );

        assertThat(dto.getId()).isEqualTo("id1");
        assertThat(dto.getPeriod()).isEqualTo("202501");
        assertThat(dto.getProduct()).isEqualTo(product);
    }

    @Test
    @DisplayName("Verifica los métodos equals, hashCode y toString")
    void testEqualsHashCodeAndToString() {
        LocalDateTime now = LocalDateTime.now();
        ProductCodeDescriptionDTO product = new ProductCodeDescriptionDTO("p1", "c1", "Prod 1");
        UserNameAndEmailDto user = new UserNameAndEmailDto("u1", "email", "User 1");

        ExtractResDto dto1 = new ExtractResDto(
                "id1", "202501", "ACTIVO", 50, now, "Detalles", product, user
        );
        ExtractResDto dto2 = new ExtractResDto(
                "id1", "202501", "ACTIVO", 50, now, "Detalles", product, user
        );
        ExtractResDto dto3 = new ExtractResDto(
                "id2", "202502", "ERROR", 100, now, "Otro", null, null
        );

        assertThat(dto1)
                .isEqualTo(dto2)
                .hasSameHashCodeAs(dto2);

        assertThat(dto1)
                .isNotEqualTo(dto3);

        assertThat(dto1.hashCode()).isNotEqualTo(dto3.hashCode());

        assertThat(dto1.toString()).contains("id1", "202501", "ACTIVO");
    }
}