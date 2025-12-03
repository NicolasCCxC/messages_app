package avvillas.core.web.traits;

import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.service.dto.util.PageResponse;
import avvillas.core.web.controller.ApiResponse;
import lombok.AllArgsConstructor;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Component;

import java.util.Collections;
import java.util.List;
import java.util.Objects;

@Component
@AllArgsConstructor
public class ProductTrait {

    private final CommunicationBetweenServices communication;

    private static final String LOADER_SERVICE = "loader";

    public List<ProductCodeDescriptionDTO> searchProducts(String searchTerm) {
        String resource = "product?search=" + searchTerm + "&getAll=true";
        ResponseEntity<ApiResponse<PageResponse<ProductDTO>>> response = communication.communicateWithMicroservice(
                LOADER_SERVICE,
                resource,
                HttpMethod.GET,
                null,
                new ParameterizedTypeReference<>() {
                }
        );
        return Objects.requireNonNull(response.getBody()).getData().getContent()
                .stream()
                .map(p -> new ProductCodeDescriptionDTO(p.getId(), p.getCode(), p.getDescription()))
                .toList();
    }

    public List<ProductCodeDescriptionDTO> getProductsByIds(List<String> ids) {
        if (ids == null || ids.isEmpty()) {
            return Collections.emptyList();
        }

        String resource = "product/bulk";
        ResponseEntity<ApiResponse<List<ProductCodeDescriptionDTO>>> response = communication.communicateWithMicroservice(
                LOADER_SERVICE,
                resource,
                HttpMethod.POST,
                ids,
                new ParameterizedTypeReference<>() {
                }
        );

        return Objects.requireNonNull(response.getBody()).getData();
    }
}
