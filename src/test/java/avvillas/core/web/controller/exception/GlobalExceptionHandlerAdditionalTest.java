package avvillas.core.web.controller.exception;

import avvillas.core.web.controller.ApiResponse;
import jakarta.persistence.EntityNotFoundException;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.dao.DataIntegrityViolationException;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.BeanPropertyBindingResult;
import org.springframework.validation.FieldError;
import org.springframework.web.bind.MethodArgumentNotValidException;

import java.util.NoSuchElementException;

import static org.assertj.core.api.Assertions.assertThat;

class GlobalExceptionHandlerAdditionalTest {

    private final GlobalExceptionHandler handler = new GlobalExceptionHandler();

    @Test
    @DisplayName("handleNoSuchElementException should return 404 with exception message")
    void handleNoSuchElementException_shouldReturnNotFound() {
        NoSuchElementException ex = new NoSuchElementException("Resource not found");

        ResponseEntity<ApiResponse<Object>> response = handler.handleNoSuchElementException(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(404);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage().get(0)).isEqualTo("Resource not found");
    }

    @Test
    @DisplayName("handleDataIntegrityViolationException should return 409 with exception message")
    void handleDataIntegrityViolationException_shouldReturnConflict() {
        DataIntegrityViolationException ex = new DataIntegrityViolationException("Duplicate key");

        ResponseEntity<ApiResponse<Object>> response = handler.handleDataIntegrityViolationException(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(409);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage().get(0)).isEqualTo("Duplicate key");
    }

    @Test
    @DisplayName("handleEntityNotFoundException should return 404 with exception message")
    void handleEntityNotFoundException_shouldReturnNotFound() {
        EntityNotFoundException ex = new EntityNotFoundException("Entity missing");

        ResponseEntity<ApiResponse<Object>> response = handler.handleEntityNotFoundException(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(404);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage().get(0)).isEqualTo("Entity missing");
    }

    @Test
    @DisplayName("handleValidationExceptions should return 400 with aggregated field error messages")
    void handleValidationExceptions_shouldReturnBadRequestWithMessages() throws Exception {
        // Prepare a BindingResult with two field errors
        BeanPropertyBindingResult bindingResult = new BeanPropertyBindingResult(new Object(), "testObject");
        bindingResult.addError(new FieldError("testObject", "field1", null, false, null, null, "Field1 is invalid"));
        bindingResult.addError(new FieldError("testObject", "field2", null, false, null, null, "Field2 is required"));

        MethodArgumentNotValidException ex = new MethodArgumentNotValidException(null, bindingResult);

        ResponseEntity<ApiResponse<Object>> response = handler.handleValidationExceptions(ex);

        assertThat(response.getStatusCode().value()).isEqualTo(400);
        assertThat(response.getBody()).isNotNull();
        assertThat(response.getBody().getMessage()).containsExactly("Field1 is invalid", "Field2 is required");
    }
}
