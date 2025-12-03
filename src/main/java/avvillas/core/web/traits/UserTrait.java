package avvillas.core.web.traits;

import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.util.PageResponse;
import avvillas.core.web.controller.ApiResponse;
import lombok.RequiredArgsConstructor;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Component;

import java.util.Collections;
import java.util.List;
import java.util.Objects;

@Component
@RequiredArgsConstructor
public class UserTrait {
    private final CommunicationBetweenServices communication;

    public List<UserNameAndEmailDto> searchUsers(String searchTerm) {
        if (searchTerm == null || searchTerm.isBlank()) {
            return Collections.emptyList();
        }

        String resource = "user?search=" + searchTerm + "&getAll=true";

        ResponseEntity<ApiResponse<PageResponse<UserNameAndEmailDto>>> response = communication.communicateWithMicroservice(
                "security",
                resource,
                HttpMethod.GET,
                null,
                new ParameterizedTypeReference<>() {
                }
        );

        return Objects.requireNonNull(response.getBody()).getData().getContent();
    }

    public List<UserNameAndEmailDto> getUsersByIds(List<String> ids) {
        if (ids == null || ids.isEmpty()) {
            return Collections.emptyList();
        }

        String resource = "user/bulk";
        ResponseEntity<ApiResponse<List<UserNameAndEmailDto>>> response = communication.communicateWithMicroservice(
                "security",
                resource,
                HttpMethod.POST,
                ids,
                new ParameterizedTypeReference<>() {
                }
        );

        return Objects.requireNonNull(response.getBody()).getData();
    }
}