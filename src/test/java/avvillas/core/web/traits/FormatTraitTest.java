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

import static avvillas.core.constant.message.EntryMessage.ERROR_GET_FORMAT;
import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertThrows;
import static org.junit.jupiter.api.Assertions.assertNotNull;

import static org.mockito.ArgumentMatchers.isNull;

import static org.mockito.Mockito.when;
import static org.mockito.Mockito.any;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.eq;
import static org.mockito.Mockito.anyString;

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
        String productId = "123";
        String token = "token-abc";
        FormatDto formatDto = new FormatDto();
        formatDto.setId("fmt-1");
        ApiResponse<FormatDto> body = ApiResponse.success(formatDto, "ok");
        ResponseEntity<ApiResponse<FormatDto>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("template-admin"),
                eq("formats/product/" + productId),
                eq(HttpMethod.GET),
                isNull(),
                any(ParameterizedTypeReference.class),
                eq(token)
        )).thenReturn(response);

        FormatDto result = formatTrait.getFormatByProductId(productId, token);

        assertNotNull(result);
        assertEquals("fmt-1", result.getId());

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
        String productId = "999";
        String token = "tk";
        HttpClientErrorException httpEx = HttpClientErrorException.create(HttpStatus.NOT_FOUND, "Not Found", null, "{\"message\":[\"format not found\"]}".getBytes(), null);

        when(communication.communicateWithMicroservice(
                anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()
        )).thenThrow(httpEx);

        NoSuchElementException thrown = assertThrows(NoSuchElementException.class, () ->
                formatTrait.getFormatByProductId(productId, token)
        );
        assertEquals("format not found", thrown.getMessage());
    }

    @Test
    void getFormatByProductId_throwsNullPointerWhenBodyIsNull() {
        String productId = "123";
        String token = "token";
        ResponseEntity<ApiResponse<FormatDto>> response = new ResponseEntity<>(null, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()
        )).thenReturn(response);

        assertThrows(NullPointerException.class, () -> formatTrait.getFormatByProductId(productId, token));
    }

    @Test
    void getFormatByProductId_usesFallbackMessageWhenParsingFails() {
        String productId = "777";
        String token = "tok";
        HttpClientErrorException httpEx = HttpClientErrorException.create(
                HttpStatus.BAD_REQUEST,
                "Bad Request",
                null,
                "plain-text-error".getBytes(),
                null
        );

        when(communication.communicateWithMicroservice(
                anyString(), anyString(), any(HttpMethod.class), isNull(), any(ParameterizedTypeReference.class), anyString()
        )).thenThrow(httpEx);

        NoSuchElementException thrown = assertThrows(NoSuchElementException.class,
                () -> formatTrait.getFormatByProductId(productId, token));
        assertEquals(ERROR_GET_FORMAT, thrown.getMessage());
    }

    @Test
    void getFormatByProductId_returnsNullWhenHandlerReturnsNormally() {
        String productId = "888";
        String token = "tok";
        HttpClientErrorException httpEx = HttpClientErrorException.create(HttpStatus.NOT_FOUND, "Not Found", null, "{\"message\":[\"x\"]}".getBytes(), null);

        when(communication.communicateWithMicroservice(
                anyString(), anyString(), any(HttpMethod.class), isNull(), any(ParameterizedTypeReference.class), anyString()
        )).thenThrow(httpEx);

        assertThrows(NoSuchElementException.class, () -> formatTrait.getFormatByProductId(productId, token));
    }
}
