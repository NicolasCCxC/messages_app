package avvillas.core.web.controller.configuration;

import avvillas.core.web.configuration.CustomJwtAuthenticationConverter;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.oauth2.jwt.Jwt;

import java.time.Instant;
import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;

class CustomJwtAuthenticationConverterTest {

    private CustomJwtAuthenticationConverter converter;

    @BeforeEach
    void setUp() {
        converter = new CustomJwtAuthenticationConverter();
    }

    private Jwt createMockJwt(Map<String, Object> claims) {
        Jwt jwt = mock(Jwt.class);
        when(jwt.getClaims()).thenReturn(claims);
        when(jwt.getSubject()).thenReturn("test-user");
        when(jwt.getTokenValue()).thenReturn("fake-token");
        when(jwt.getIssuedAt()).thenReturn(Instant.now());
        when(jwt.getExpiresAt()).thenReturn(Instant.now().plusSeconds(3600));

        // --- CORRECCIÓN AQUÍ ---
        // Mockear getClaim para que devuelva el valor del mapa de claims
        when(jwt.getClaim(anyString())).thenAnswer(invocation -> {
            String claimName = invocation.getArgument(0);
            return claims.get(claimName);
        });

        return jwt;
    }

    @Test
    @DisplayName("Verifica que convierta el claim 'roles' en authorities sin prefijo")
    void shouldConvertRolesClaimToAuthoritiesWithoutPrefix() {
        Map<String, Object> claims = Map.of("roles", List.of("ROLE_ADMIN", "ROLE_USER"));
        Jwt mockJwt = createMockJwt(claims);

        Authentication authentication = converter.convert(mockJwt);

        Collection<String> authorities = authentication.getAuthorities().stream()
                .map(GrantedAuthority::getAuthority)
                .collect(Collectors.toList());

        assertThat(authorities)
                .hasSize(2)
                .containsExactlyInAnyOrder("ROLE_ADMIN", "ROLE_USER")
                .doesNotContain("SCOPE_ROLE_ADMIN");
    }

    @Test
    @DisplayName("Verifica que maneje un JWT sin el claim 'roles'")
    void shouldHandleJwtWithNoRolesClaim() {
        Map<String, Object> claims = Map.of("sub", "user");
        Jwt mockJwt = createMockJwt(claims);

        Authentication authentication = converter.convert(mockJwt);

        assertThat(authentication.getAuthorities()).isEmpty();
    }

    @Test
    @DisplayName("Verifica que maneje un claim 'roles' vacío")
    void shouldHandleJwtWithEmptyRolesClaim() {
        Map<String, Object> claims = Map.of("roles", Collections.emptyList());
        Jwt mockJwt = createMockJwt(claims);

        Authentication authentication = converter.convert(mockJwt);

        assertThat(authentication.getAuthorities()).isEmpty();
    }
}