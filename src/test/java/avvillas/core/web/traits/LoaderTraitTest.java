package avvillas.core.web.traits;

import avvillas.core.service.dto.content_index_file.ContentIndexFileDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.service.dto.product.ProductDTO;
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
class LoaderTraitTest {

    @Mock
    private CommunicationBetweenServices communication;

    private LoaderTrait loaderTrait;

    @BeforeEach
    void setUp() {
        loaderTrait = new LoaderTrait(communication);
    }

    @Test
    void getProductById_returnsProductOnSuccess() {
        String id = "p-1";
        String token = "tk";
        ProductDTO product = new ProductDTO();
        product.setId("p-1");
        ApiResponse<ProductDTO> body = ApiResponse.success(product, "ok");
        ResponseEntity<ApiResponse<ProductDTO>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("loader"),
                eq("product/" + id),
                eq(HttpMethod.GET),
                isNull(),
                any(ParameterizedTypeReference.class),
                eq(token)
        )).thenReturn(response);

        ProductDTO result = loaderTrait.getProductById(id, token);
        assertNotNull(result);
        assertEquals("p-1", result.getId());

        ArgumentCaptor<ParameterizedTypeReference<ApiResponse<ProductDTO>>> typeRefCaptor = ArgumentCaptor.forClass(ParameterizedTypeReference.class);
        verify(communication).communicateWithMicroservice(
                eq("loader"),
                eq("product/" + id),
                eq(HttpMethod.GET),
                isNull(),
                typeRefCaptor.capture(),
                eq(token)
        );
        assertNotNull(typeRefCaptor.getValue());
    }

    @Test
    void getProductById_throwsNoSuchElementWhenHttpClientError() {
        String id = "missing";
        String token = "tk";
        HttpClientErrorException httpEx = HttpClientErrorException.create(HttpStatus.NOT_FOUND, "Not Found", null, "{\"message\":[\"product not found\"]}".getBytes(), null);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()))
                .thenThrow(httpEx);

        NoSuchElementException thrown = assertThrows(NoSuchElementException.class, () -> loaderTrait.getProductById(id, token));
        assertEquals("product not found", thrown.getMessage());
    }

    @Test
    void getProductById_throwsNullPointerWhenBodyIsNull() {
        String id = "p";
        String token = "tk";
        ResponseEntity<ApiResponse<ProductDTO>> response = new ResponseEntity<>(null, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()))
                .thenReturn(response);
        assertThrows(NullPointerException.class, () -> loaderTrait.getProductById(id, token));
    }

    @Test
    void getIndexFilePathByProductId_returnsPathOnSuccess() {
        String id = "p-2";
        PathExtractsArchiveIndexDto dto = new PathExtractsArchiveIndexDto();
        ApiResponse<PathExtractsArchiveIndexDto> body = ApiResponse.success(dto, "ok");
        ResponseEntity<ApiResponse<PathExtractsArchiveIndexDto>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("loader"),
                eq("path-extracts-archive-index/" + id),
                eq(HttpMethod.GET),
                isNull(),
                any(ParameterizedTypeReference.class)
        )).thenReturn(response);

        PathExtractsArchiveIndexDto result = loaderTrait.getIndexFilePathByProductId(id);
        assertNotNull(result);

        ArgumentCaptor<ParameterizedTypeReference<ApiResponse<PathExtractsArchiveIndexDto>>> captor = ArgumentCaptor.forClass(ParameterizedTypeReference.class);
        verify(communication).communicateWithMicroservice(eq("loader"), eq("path-extracts-archive-index/" + id), eq(HttpMethod.GET), isNull(), captor.capture());
        assertNotNull(captor.getValue());
    }

    @Test
    void getIndexFilePathByProductId_throwsNoSuchElementWhenHttpClientError() {
        String id = "missing";
        HttpClientErrorException httpEx = HttpClientErrorException.create(HttpStatus.NOT_FOUND, "Not Found", null, "{\"message\":[\"path not found\"]}".getBytes(), null);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenThrow(httpEx);
        NoSuchElementException thrown = assertThrows(NoSuchElementException.class, () -> loaderTrait.getIndexFilePathByProductId(id));
        assertEquals("path not found", thrown.getMessage());
    }

    @Test
    void getIndexFilePathByProductId_throwsNullPointerWhenBodyIsNull() {
        String id = "p";
        ResponseEntity<ApiResponse<PathExtractsArchiveIndexDto>> response = new ResponseEntity<>(null, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenReturn(response);
        assertThrows(NullPointerException.class, () -> loaderTrait.getIndexFilePathByProductId(id));
    }

    @Test
    void getContentFileByProductId_returnsContentOnSuccess() {
        String id = "p-3";
        String token = "tk";
        ContentIndexFileDto dto = new ContentIndexFileDto();
        ApiResponse<ContentIndexFileDto> body = ApiResponse.success(dto, "ok");
        ResponseEntity<ApiResponse<ContentIndexFileDto>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("loader"),
                eq("content-index-file/" + id),
                eq(HttpMethod.GET),
                isNull(),
                any(ParameterizedTypeReference.class),
                eq(token)
        )).thenReturn(response);

        ContentIndexFileDto result = loaderTrait.getContentFileByProductId(id, token);
        assertNotNull(result);

        ArgumentCaptor<ParameterizedTypeReference<ApiResponse<ContentIndexFileDto>>> captor = ArgumentCaptor.forClass(ParameterizedTypeReference.class);
        verify(communication).communicateWithMicroservice(eq("loader"), eq("content-index-file/" + id), eq(HttpMethod.GET), isNull(), captor.capture(), eq(token));
        assertNotNull(captor.getValue());
    }

    @Test
    void getContentFileByProductId_throwsNoSuchElementWhenHttpClientError() {
        String id = "missing";
        String token = "tk";
        HttpClientErrorException httpEx = HttpClientErrorException.create(HttpStatus.NOT_FOUND, "Not Found", null, "{\"message\":[\"content not found\"]}".getBytes(), null);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()))
                .thenThrow(httpEx);
        NoSuchElementException thrown = assertThrows(NoSuchElementException.class, () -> loaderTrait.getContentFileByProductId(id, token));
        assertEquals("content not found", thrown.getMessage());
    }

    @Test
    void getContentFileByProductId_throwsNullPointerWhenBodyIsNull() {
        String id = "p";
        String token = "tk";
        ResponseEntity<ApiResponse<ContentIndexFileDto>> response = new ResponseEntity<>(null, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class), anyString()))
                .thenReturn(response);
        assertThrows(NullPointerException.class, () -> loaderTrait.getContentFileByProductId(id, token));
    }
}