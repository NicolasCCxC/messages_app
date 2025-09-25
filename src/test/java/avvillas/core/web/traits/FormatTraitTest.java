package avvillas.core.web.traits;

import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.web.controller.ApiResponse;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpMethod;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.client.HttpClientErrorException;

import java.util.NoSuchElementException;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class FormatTraitTest {

    @Mock
    private CommunicationBetweenServices communication;

    private FormatTrait formatTrait;

    @BeforeEach
    void setUp() {
        formatTrait = new FormatTrait(communication);
    }

    @Test
    void getFormatByProductId_returnsFormatOnSuccess() {
        // Arrange
        String productId = "123";
        String token = "token-abc";
        FormatDto formatDto = new FormatDto();
        formatDto.setId("fmt-1");
        ApiResponse<FormatDto> body = ApiResponse.success(formatDto, "ok", null);
        ResponseEntity<ApiResponse<FormatDto>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("template-admin"),
                eq("formats/product/" + productId),
                eq(HttpMethod.GET),
                isNull(),
                any(ParameterizedTypeReference.class),
                eq(token)
        )).thenReturn(response);

        // Act
        FormatDto result = formatTrait.getFormatByProductId(productId, token);

        // Assert
        assertNotNull(result);
        assertEquals("fmt-1", result.getId());

        // Verify interaction and that a ParameterizedTypeReference was provided
        ArgumentCaptor<ParameterizedTypeReference<ApiResponse<FormatDto>>> typeRefCaptor = ArgumentCaptor.forClass(ParameterizedTypeReference.class);
        verify(communication).communicateWithMicroservice(
                eq("template-admin"),
                eq("formats/product/" + productId),
                eq(HttpMethod.GET),
                isNull(),
                typeRefCaptor.capture(),
                eq(token)
        );
        assertNotNull(typeRefCaptor.getValue());
    }

    @Test
    void getFormatByProductId_throwsNoSuchElementWhenHttpClientError() {
        // Arrange
        String productId = "999";
        String token = "tk";
        HttpClientErrorException httpEx = HttpClientErrorException.create(HttpStatus.NOT_FOUND, "Not Found", null, "{\"message\":[\"format not found\"]}".getBytes(), null);

        when(communication.communicateWithMicroservice(
                anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()
        )).thenThrow(httpEx);

        // Act + Assert
        NoSuchElementException thrown = assertThrows(NoSuchElementException.class, () ->
                formatTrait.getFormatByProductId(productId, token)
        );
        // The HttpClientErrorHandler rethrows as NoSuchElementException with parsed message
        assertEquals("format not found", thrown.getMessage());
    }

    @Test
    void getFormatByProductId_throwsNullPointerWhenBodyIsNull() {
        // Arrange
        String productId = "123";
        String token = "token";
        ResponseEntity<ApiResponse<FormatDto>> response = new ResponseEntity<>(null, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()
        )).thenReturn(response);

        // Act + Assert
        assertThrows(NullPointerException.class, () -> formatTrait.getFormatByProductId(productId, token));
    }
}
