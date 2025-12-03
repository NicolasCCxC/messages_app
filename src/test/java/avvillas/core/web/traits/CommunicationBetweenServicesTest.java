package avvillas.core.web.traits;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.Mock;
import org.mockito.ArgumentCaptor;
import org.mockito.MockedStatic;
import org.mockito.Mockito;
import org.mockito.MockitoAnnotations;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.http.HttpEntity;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.context.SecurityContext;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.jwt.Jwt;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.web.client.RestTemplate;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@ActiveProfiles("test")
@DisplayName("CommunicationBetweenServices Tests")
class CommunicationBetweenServicesTest {

    @Mock
    private RestTemplate restTemplate;

    @Mock
    private SecurityContext securityContext;

    @Mock
    private JwtAuthenticationToken jwtAuthenticationToken;

    @Mock
    private Jwt jwt;

    @Mock
    private ResponseEntity<String> responseEntity;

    private CommunicationBetweenServices communicationBetweenServices;

    private static final String GATEWAY_URL = "http://gateway";
    private static final String SERVICE = "test-service";
    private static final String RESOURCE = "test-resource";
    private static final String JWT_TOKEN = "test-jwt-token";
    private static final String FAKE_TOKEN = "";

    @BeforeEach
    void setUp() {
        MockitoAnnotations.openMocks(this);

        communicationBetweenServices = new CommunicationBetweenServices(
                restTemplate,
                GATEWAY_URL,
                FAKE_TOKEN
        );

        when(responseEntity.getBody()).thenReturn("Test Response");
        when(restTemplate.exchange(
                anyString(),
                any(HttpMethod.class),
                any(HttpEntity.class),
                any(ParameterizedTypeReference.class)
        )).thenReturn(responseEntity);
    }

    @Test
    @DisplayName("Should use JWT token from SecurityContextHolder when fake token is empty")
    void shouldUseJwtTokenFromSecurityContextWhenFakeTokenIsEmpty() {
        when(jwt.getTokenValue()).thenReturn(JWT_TOKEN);
        when(jwtAuthenticationToken.getToken()).thenReturn(jwt);

        try (MockedStatic<SecurityContextHolder> mockedStatic = Mockito.mockStatic(SecurityContextHolder.class)) {
            mockedStatic.when(SecurityContextHolder::getContext).thenReturn(securityContext);
            when(securityContext.getAuthentication()).thenReturn(jwtAuthenticationToken);

            communicationBetweenServices.communicateWithMicroservice(
                    SERVICE,
                    RESOURCE,
                    HttpMethod.GET,
                    null,
                    new ParameterizedTypeReference<String>() {
                    }
            );

            ArgumentCaptor<HttpEntity<?>> httpEntityCaptor = ArgumentCaptor.forClass(HttpEntity.class);
            verify(restTemplate).exchange(
                    eq(GATEWAY_URL + "/" + SERVICE + "/" + RESOURCE),
                    eq(HttpMethod.GET),
                    httpEntityCaptor.capture(),
                    any(ParameterizedTypeReference.class)
            );

            HttpEntity<?> capturedEntity = httpEntityCaptor.getValue();
            HttpHeaders headers = capturedEntity.getHeaders();
            assertEquals("Bearer " + JWT_TOKEN, headers.getFirst("Authorization"));
        }
    }
}