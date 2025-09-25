package avvillas.core.web.traits;

import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.service.dto.util.PageResponse;
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

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class ProductTraitTest {

    @Mock
    private CommunicationBetweenServices communication;

    private ProductTrait productTrait;

    @BeforeEach
    void setUp() {
        productTrait = new ProductTrait(communication);
    }

    @Test
    void searchProducts_mapsPageContentToCodeDescription_onSuccess() {
        String term = "abc";

        ProductDTO p1 = new ProductDTO();
        p1.setId("1");
        p1.setCode("C1");
        p1.setDescription("D1");
        ProductDTO p2 = new ProductDTO();
        p2.setId("2");
        p2.setCode("C2");
        p2.setDescription("D2");

        PageResponse<ProductDTO> page = new PageResponse<>();
        page.setContent(Arrays.asList(p1, p2));

        ApiResponse<PageResponse<ProductDTO>> body = ApiResponse.success(page, "ok", null);
        ResponseEntity<ApiResponse<PageResponse<ProductDTO>>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("loader"),
                eq("product?search=" + term + "&getAll=true"),
                eq(HttpMethod.GET),
                isNull(),
                any(ParameterizedTypeReference.class)
        )).thenReturn(response);

        List<ProductCodeDescriptionDTO> result = productTrait.searchProducts(term);
        assertNotNull(result);
        assertEquals(2, result.size());
        assertEquals("1", result.get(0).getId());
        assertEquals("C2", result.get(1).getCode());

        ArgumentCaptor<ParameterizedTypeReference<ApiResponse<PageResponse<ProductDTO>>>> captor = ArgumentCaptor.forClass(ParameterizedTypeReference.class);
        verify(communication).communicateWithMicroservice(eq("loader"), eq("product?search=" + term + "&getAll=true"), eq(HttpMethod.GET), isNull(), captor.capture());
        assertNotNull(captor.getValue());
    }

    @Test
    void searchProducts_returnsEmptyList_whenExceptionThrown() {
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenThrow(new RuntimeException("boom"));
        List<ProductCodeDescriptionDTO> result = productTrait.searchProducts("x");
        assertNotNull(result);
        assertTrue(result.isEmpty());
    }

    @Test
    void searchProducts_returnsEmptyList_whenBodyOrDataOrContentNull() {
        // Body null
        ResponseEntity<ApiResponse<PageResponse<ProductDTO>>> nullBody = new ResponseEntity<>(null, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenReturn(nullBody);
        assertTrue(productTrait.searchProducts("x").isEmpty());

        // Data null
        ApiResponse<PageResponse<ProductDTO>> bodyNullData = ApiResponse.success(null, "ok", null);
        ResponseEntity<ApiResponse<PageResponse<ProductDTO>>> responseNullData = new ResponseEntity<>(bodyNullData, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenReturn(responseNullData);
        assertTrue(productTrait.searchProducts("x").isEmpty());

        // Content null
        PageResponse<ProductDTO> pageNoContent = new PageResponse<>();
        pageNoContent.setContent(null);
        ApiResponse<PageResponse<ProductDTO>> bodyNoContent = ApiResponse.success(pageNoContent, "ok", null);
        ResponseEntity<ApiResponse<PageResponse<ProductDTO>>> responseNoContent = new ResponseEntity<>(bodyNoContent, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenReturn(responseNoContent);
        assertTrue(productTrait.searchProducts("x").isEmpty());
    }

    @Test
    void getProductsByIds_returnsEmpty_whenNullOrEmptyIds() {
        assertTrue(productTrait.getProductsByIds(null).isEmpty());
        assertTrue(productTrait.getProductsByIds(Collections.emptyList()).isEmpty());
    }

    @Test
    void getProductsByIds_returnsData_onSuccess() {
        List<String> ids = Arrays.asList("1", "2");
        List<ProductCodeDescriptionDTO> data = new ArrayList<>();
        data.add(new ProductCodeDescriptionDTO("1", "C1", "D1"));
        data.add(new ProductCodeDescriptionDTO("2", "C2", "D2"));

        ApiResponse<List<ProductCodeDescriptionDTO>> body = ApiResponse.success(data, "ok", null);
        ResponseEntity<ApiResponse<List<ProductCodeDescriptionDTO>>> response = new ResponseEntity<>(body, HttpStatus.OK);

        when(communication.communicateWithMicroservice(
                eq("loader"),
                eq("product/bulk"),
                eq(HttpMethod.POST),
                eq(ids),
                any(ParameterizedTypeReference.class)
        )).thenReturn(response);

        List<ProductCodeDescriptionDTO> result = productTrait.getProductsByIds(ids);
        assertEquals(2, result.size());
        assertEquals("C1", result.get(0).getCode());

        ArgumentCaptor<ParameterizedTypeReference<ApiResponse<List<ProductCodeDescriptionDTO>>>> captor = ArgumentCaptor.forClass(ParameterizedTypeReference.class);
        verify(communication).communicateWithMicroservice(eq("loader"), eq("product/bulk"), eq(HttpMethod.POST), eq(ids), captor.capture());
        assertNotNull(captor.getValue());
    }

    @Test
    void getProductsByIds_returnsEmpty_whenExceptionOrNulls() {
        List<String> ids = Arrays.asList("1");
        // Exception
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenThrow(new RuntimeException("boom"));
        assertTrue(productTrait.getProductsByIds(ids).isEmpty());

        // Body null
        reset(communication);
        ResponseEntity<ApiResponse<List<ProductCodeDescriptionDTO>>> nullBody = new ResponseEntity<>(null, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenReturn(nullBody);
        assertTrue(productTrait.getProductsByIds(ids).isEmpty());

        // Data null
        ApiResponse<List<ProductCodeDescriptionDTO>> bodyNullData = ApiResponse.success(null, "ok", null);
        ResponseEntity<ApiResponse<List<ProductCodeDescriptionDTO>>> responseNullData = new ResponseEntity<>(bodyNullData, HttpStatus.OK);
        when(communication.communicateWithMicroservice(anyString(), anyString(), any(), any(), any(ParameterizedTypeReference.class)))
                .thenReturn(responseNullData);
        assertTrue(productTrait.getProductsByIds(ids).isEmpty());
    }
}
