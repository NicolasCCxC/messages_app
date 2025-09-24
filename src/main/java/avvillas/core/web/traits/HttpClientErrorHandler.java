package avvillas.core.web.traits;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.databind.ObjectMapper;
import lombok.Getter;
import lombok.extern.slf4j.Slf4j;
import org.springframework.web.client.HttpClientErrorException;

import java.util.List;
import java.util.NoSuchElementException;

@Slf4j
public class HttpClientErrorHandler {

    private HttpClientErrorHandler() {}

    public static void handle(HttpClientErrorException e, String message) {
        String errorResponseBody = e.getResponseBodyAsString();
        String errorMessage = message;

        try {
            errorMessage = parseErrorResponse(errorResponseBody);
        } catch (Exception ex) {
            log.info("Error parsing response: {}", ex.getMessage());
        }
        throw new NoSuchElementException(errorMessage);
    }

    private static String parseErrorResponse(String responseBody) throws Exception {
        ObjectMapper objectMapper = new ObjectMapper();
        ErrorResponse errorResponse = objectMapper.readValue(responseBody, ErrorResponse.class);
        return errorResponse.getMessage().get(0);
    }

    @Getter
    @JsonIgnoreProperties(ignoreUnknown = true)
    static class ErrorResponse {
        @SuppressWarnings("unused")
        private List<String> message;
    }
}