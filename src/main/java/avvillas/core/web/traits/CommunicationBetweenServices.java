package avvillas.core.web.traits;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpEntity;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;
import org.springframework.stereotype.Component;
import org.springframework.web.client.RestTemplate;

@Component
public class CommunicationBetweenServices {

    private final String gatewayUrl;
    private final String fakeToken;
    private final RestTemplate restTemplate;
    public static final String AUTHORIZATION = "Authorization";
    public static final String BEARER = "Bearer ";

    @Autowired
    public CommunicationBetweenServices(RestTemplate restTemplate,
                                        @Value("${gateway.url}") String gatewayUrl,
                                        @Value("${FAKE_TOKEN}") String fakeToken
    ) {
        this.restTemplate = restTemplate;
        this.gatewayUrl = gatewayUrl;
        this.fakeToken = fakeToken;
    }
    /**
     * @param service     - The name of the service to communicate with.
     * @param resource    - The resource or endpoint of the service.
     * @param httpMethod  - The HTTP method to use (GET, POST, PUT, DELETE, etc.).
     * @param requestBody - The body of the request (optional, can be null).
     * @return - The response from the microservice.
     */
    public <T> ResponseEntity<T> communicateWithMicroservice(String service, String resource, HttpMethod httpMethod,
                                                             Object requestBody, ParameterizedTypeReference<T> responseType) {
        return communicateWithMicroservice(service, resource, httpMethod, requestBody, responseType, null);
    }

    public <T> ResponseEntity<T> communicateWithMicroservice(String service, String resource, HttpMethod httpMethod,
                                                             Object requestBody, ParameterizedTypeReference<T> responseType,
                                                             String providedToken) {
        String url = gatewayUrl + "/" + service + "/" + resource;
        HttpHeaders headers = new HttpHeaders();
        String token;

        if (providedToken != null && !providedToken.trim().isEmpty()) {
            token = providedToken;
        } else if (fakeToken != null && !fakeToken.trim().isEmpty()) {
            token = fakeToken;
        } else {
            JwtAuthenticationToken authentication = (JwtAuthenticationToken) SecurityContextHolder.getContext().getAuthentication();
            token = authentication.getToken().getTokenValue();
        }
        headers.set(AUTHORIZATION, BEARER + token);
        HttpEntity<Object> httpEntity = new HttpEntity<>(requestBody, headers);

        return restTemplate.exchange(url, httpMethod, httpEntity, responseType);
    }
}

