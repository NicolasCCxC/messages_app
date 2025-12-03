package avvillas.core.web.traits;

import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.web.controller.ApiResponse;
import lombok.AllArgsConstructor;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Component;
import org.springframework.web.client.HttpClientErrorException;

import java.util.Objects;

import static avvillas.core.constant.message.EntryMessage.ERROR_GET_FORMAT;

@Component
@AllArgsConstructor
public class FormatTrait {

    private final CommunicationBetweenServices communication;

    public FormatDto getFormatByProductId(String id, String token) {

        try {
            String resource = "formats/product/" + id;

            ResponseEntity<ApiResponse<FormatDto>> response = communication.communicateWithMicroservice(
                    "template-admin",
                    resource,
                    HttpMethod.GET,
                    null,
                    new ParameterizedTypeReference<>() {
                    },
                    token
            );

            return Objects.requireNonNull(response.getBody()).getData();
        } catch (HttpClientErrorException e) {
            HttpClientErrorHandler.handle(e, ERROR_GET_FORMAT);
            return null;
        }
    }
}
