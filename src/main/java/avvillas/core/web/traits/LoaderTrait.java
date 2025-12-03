package avvillas.core.web.traits;

import avvillas.core.constant.MessageConstant;
import avvillas.core.constant.message.EntryMessage;
import avvillas.core.service.dto.content_index_file.ContentIndexFileDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.web.controller.ApiResponse;
import lombok.AllArgsConstructor;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Component;
import org.springframework.web.client.HttpClientErrorException;

import java.util.Objects;

@Component
@AllArgsConstructor
public class LoaderTrait {

    private final CommunicationBetweenServices communication;
    private static final String LOADER_SERVICE = "loader";
    private static final String RESOURCE_PATH_PRODUCT = "product/";
    private static final String RESOURCE_PATH_EXTRACTS_ARCHIVE_INDEX = "path-extracts-archive-index/";
    private static final String RESOURCE_PATH_CONTENT_INDEX_FILE = "content-index-file/";

    public ProductDTO getProductById(String id, String token) {
        try {
            String resource = RESOURCE_PATH_PRODUCT + id;
            ResponseEntity<ApiResponse<ProductDTO>> response = communication.communicateWithMicroservice(
                    LOADER_SERVICE,
                    resource,
                    HttpMethod.GET,
                    null,
                    new ParameterizedTypeReference<>() {
                    },
                    token
            );
            return Objects.requireNonNull(response.getBody()).getData();
        } catch (HttpClientErrorException e) {
            HttpClientErrorHandler.handle(e, MessageConstant.format(EntryMessage.PRODUCT_NOT_FOUND, id));
            return null;
        }

    }

    public PathExtractsArchiveIndexDto getIndexFilePathByProductId(String id) {
        try {
            String resource = RESOURCE_PATH_EXTRACTS_ARCHIVE_INDEX + id;
            ResponseEntity<ApiResponse<PathExtractsArchiveIndexDto>> response = communication.communicateWithMicroservice(
                    LOADER_SERVICE,
                    resource,
                    HttpMethod.GET,
                    null,
                    new ParameterizedTypeReference<>() {
                    }
            );
            return Objects.requireNonNull(response.getBody()).getData();
        } catch (HttpClientErrorException e) {
            HttpClientErrorHandler.handle(e, MessageConstant.format(EntryMessage.PATH_EXTRACTS_ARCHIVE_INDEX_NOT_FOUND_BY_PRODUCT_ID, id));
            return null;
        }
    }

    public ContentIndexFileDto getContentFileByProductId(String id, String token) {
        try {
            String resource = RESOURCE_PATH_CONTENT_INDEX_FILE + id;
            ResponseEntity<ApiResponse<ContentIndexFileDto>> response = communication.communicateWithMicroservice(
                    LOADER_SERVICE,
                    resource,
                    HttpMethod.GET,
                    null,
                    new ParameterizedTypeReference<>() {
                    },
                    token
            );
            return Objects.requireNonNull(response.getBody()).getData();
        } catch (HttpClientErrorException e) {
            HttpClientErrorHandler.handle(e, MessageConstant.format(EntryMessage.CONTENT_NOT_FOUND_BY_PRODUCT_ID, id));
            return null;
        }
    }

}
