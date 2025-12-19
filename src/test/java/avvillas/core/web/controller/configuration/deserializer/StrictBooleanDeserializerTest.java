package avvillas.core.web.controller.configuration.deserializer;

import avvillas.core.constant.message.CommonMessage;
import avvillas.core.web.configuration.deserializer.StrictBooleanDeserializer;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.annotation.JsonDeserialize;
import com.fasterxml.jackson.databind.exc.InvalidFormatException;
import lombok.Data;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;

class StrictBooleanDeserializerTest {

    private ObjectMapper objectMapper;

    @BeforeEach
    void setUp() {
        objectMapper = new ObjectMapper();
    }

    @Test
    @DisplayName("Verifica que 'true' (literal) se deserialice a Boolean.TRUE")
    void shouldDeserializeTrueLiteral() throws Exception {
        String json = "{\"value\": true}";
        TestWrapper result = objectMapper.readValue(json, TestWrapper.class);
        assertThat(result.getValue()).isTrue();
    }

    @Test
    @DisplayName("Verifica que 'false' (literal) se deserialice a Boolean.FALSE")
    void shouldDeserializeFalseLiteral() throws Exception {
        String json = "{\"value\": false}";
        TestWrapper result = objectMapper.readValue(json, TestWrapper.class);
        assertThat(result.getValue()).isFalse();
    }

    @Test
    @DisplayName("Verifica que un string 'true' lance InvalidFormatException")
    void shouldThrowExceptionForStringTrue() {
        String json = "{\"value\": \"true\"}";

        assertThatThrownBy(() -> {
            objectMapper.readValue(json, TestWrapper.class);
        })
                .isInstanceOf(InvalidFormatException.class)
                .hasMessageContaining(CommonMessage.BOOLEAN_REQUIRED);
    }

    @Test
    @DisplayName("Verifica que un número '1' lance InvalidFormatException")
    void shouldThrowExceptionForNumberOne() {
        String json = "{\"value\": 1}";

        assertThatThrownBy(() -> {
            objectMapper.readValue(json, TestWrapper.class);
        })
                .isInstanceOf(InvalidFormatException.class)
                .hasMessageContaining(CommonMessage.BOOLEAN_REQUIRED);
    }

    @Test
    @DisplayName("Verifica que 'null' (literal) se deserialice a null")
    void shouldDeserializeNullLiteralToNull() throws Exception {
        String json = "{\"value\": null}";

        TestWrapper result = objectMapper.readValue(json, TestWrapper.class);

        assertThat(result.getValue()).isNull();
    }

    @Data
    static class TestWrapper {
        @JsonDeserialize(using = StrictBooleanDeserializer.class)
        private Boolean value;
    }
}