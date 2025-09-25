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
import java.util.Optional;

@Component
@AllArgsConstructor
public class ProductTrait {

    private final CommunicationBetweenServices communication;

    private static final String LOADER_SERVICE = "loader";

    public List<ProductCodeDescriptionDTO> searchProducts(String searchTerm) {
        try {
            String resource = "product?search=" + searchTerm + "&getAll=true";
            ResponseEntity<ApiResponse<PageResponse<ProductDTO>>> response = communication.communicateWithMicroservice(
                    LOADER_SERVICE,
                    resource,
                    HttpMethod.GET,
                    null,
                    new ParameterizedTypeReference<>() {
                    }
            );
            PageResponse<ProductDTO> page = Optional.ofNullable(response.getBody())
                    .map(ApiResponse::getData)
                    .orElse(null);
            if (page == null || page.getContent() == null) {
                return Collections.emptyList();
            }
            return page.getContent()
                    .stream()
                    .map(p -> new ProductCodeDescriptionDTO(p.getId(), p.getCode(), p.getDescription()))
                    .toList();
        } catch (Exception e) {
            return Collections.emptyList();
        }
    }

    public List<ProductCodeDescriptionDTO> getProductsByIds(List<String> ids) {
        if (ids == null || ids.isEmpty()) {
            return Collections.emptyList();
        }
        try {
            String resource = "product/bulk";
            ResponseEntity<ApiResponse<List<ProductCodeDescriptionDTO>>> response = communication.communicateWithMicroservice(
                    LOADER_SERVICE,
                    resource,
                    HttpMethod.POST,
                    ids,
                    new ParameterizedTypeReference<>() {
                    }
            );

            List<ProductCodeDescriptionDTO> data = Optional.ofNullable(response.getBody())
                    .map(ApiResponse::getData)
                    .orElse(null);
            return data != null ? data : Collections.emptyList();
        } catch (Exception e) {
            return Collections.emptyList();
        }
    }
}
