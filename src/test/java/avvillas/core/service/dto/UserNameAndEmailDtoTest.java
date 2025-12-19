package avvillas.core.service.dto;

import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import static org.assertj.core.api.Assertions.assertThat;

class UserNameAndEmailDtoTest {

    @Test
    @DisplayName("Verifica el constructor sin argumentos, getters y setters")
    void testNoArgsConstructorAndSetters() {
        UserNameAndEmailDto dto = new UserNameAndEmailDto();
        dto.setId("user1");
        dto.setEmail("test@example.com");
        dto.setName("Test User");

        assertThat(dto.getId()).isEqualTo("user1");
        assertThat(dto.getEmail()).isEqualTo("test@example.com");
        assertThat(dto.getName()).isEqualTo("Test User");
    }

    @Test
    @DisplayName("Verifica el constructor con todos los argumentos")
    void testAllArgsConstructor() {
        UserNameAndEmailDto dto = new UserNameAndEmailDto("user1", "test@example.com", "Test User");

        assertThat(dto.getId()).isEqualTo("user1");
        assertThat(dto.getEmail()).isEqualTo("test@example.com");
        assertThat(dto.getName()).isEqualTo("Test User");
    }

    @Test
    @DisplayName("Verifica los métodos equals, hashCode y toString")
    void testEqualsHashCodeAndToString() {
        UserNameAndEmailDto dto1 = new UserNameAndEmailDto("user1", "test@example.com", "Test User");
        UserNameAndEmailDto dto2 = new UserNameAndEmailDto("user1", "test@example.com", "Test User");
        UserNameAndEmailDto dto3 = new UserNameAndEmailDto("user2", "test2@example.com", "Test User 2");

        assertThat(dto1)
                .isEqualTo(dto2)
                .hasSameHashCodeAs(dto2);

        assertThat(dto1)
                .isNotEqualTo(dto3);

        assertThat(dto1.hashCode()).isNotEqualTo(dto3.hashCode());

        assertThat(dto1.toString()).contains("user1", "test@example.com", "Test User");
    }
}