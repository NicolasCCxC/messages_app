package avvillas.core.web.controller.exception;

import avvillas.core.constant.message.CommonMessage;
import avvillas.core.web.controller.ApiResponse;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.http.ResponseEntity;
import org.springframework.http.converter.HttpMessageConversionException;

import static org.assertj.core.api.Assertions.assertThat;

class GlobalExceptionHandlerTest {

    private final GlobalExceptionHandler handler = new GlobalExceptionHandler();

    @Test
    @DisplayName("handleGenericException should return 500 with exception message")
    void handleGenericException_shouldReturnInternalServerError() {
        Exception ex = new Exception("Generic error occurred");

        ResponseEntity<ApiResponse<Object>> response = handler.handleGenericException(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(500);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage().get(0)).isEqualTo("Generic error occurred");
    }

    @Test
    @DisplayName("handleHttpMessageNotReadable should return 400 with EMPTY_BODY message")
    void handleHttpMessageNotReadable_shouldReturnBadRequest() {
        HttpMessageConversionException ex = new HttpMessageConversionException("Invalid JSON");

        ResponseEntity<ApiResponse<Object>> response = handler.handleHttpMessageNotReadable(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(400);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage().get(0)).isEqualTo(CommonMessage.EMPTY_BODY);
    }

    @Test
    @DisplayName("handleIllegalArgumentException should return 400 with exception message")
    void handleIllegalArgumentException_shouldReturnBadRequest() {
        IllegalArgumentException ex = new IllegalArgumentException("Invalid argument");

        ResponseEntity<ApiResponse<Object>> response = handler.handleIllegalArgumentException(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(400);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage().get(0)).isEqualTo("Invalid argument");
    }
}

