package avvillas.core.web.configuration;

import avvillas.core.web.configuration.security.filter.ApiKeyAuthFilter;
import io.jsonwebtoken.io.Decoders;
import io.jsonwebtoken.security.Keys;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.core.env.Environment;
import org.springframework.http.HttpMethod;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.oauth2.jose.jws.MacAlgorithm;
import org.springframework.security.oauth2.jwt.JwtDecoder;
import org.springframework.security.oauth2.jwt.JwtValidators;
import org.springframework.security.oauth2.jwt.NimbusJwtDecoder;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationConverter;
import org.springframework.security.oauth2.server.resource.web.BearerTokenAuthenticationEntryPoint;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;

import javax.crypto.SecretKey;

@Configuration
@EnableWebSecurity
public class SecurityConfiguration {

    private final String activeProfile;

    @Value("${api.key}")
    private String validApiKey;
    @Value("${jwt.secret}")
    private String jwtSecretKeyBase64;
    @Value("${ROLE_ID_ADMIN}")
    private String adminRoleId;
    @Value("${ROLE_ID_WRITING}")
    private String writingRoleId;
    @Value("${ROLE_ID_READING}")
    private String readingRoleId;
    @Value("${USER_PERMISSION_DENIED_MESSAGE}")
    private String userPermissionDeniedMessage;

    public SecurityConfiguration (Environment environment) {
        String[] profiles = environment.getActiveProfiles();
        this.activeProfile = profiles.length > 0 ? profiles[0] : "prod";
    }

    @Bean
    public JwtDecoder jwtDecoder() {
        byte[] keyBytes = Decoders.BASE64.decode(jwtSecretKeyBase64);
        SecretKey secretKey = Keys.hmacShaKeyFor(keyBytes);
        NimbusJwtDecoder jwtDecoder = NimbusJwtDecoder.withSecretKey(secretKey).macAlgorithm(MacAlgorithm.HS384).build();
        jwtDecoder.setJwtValidator(JwtValidators.createDefault());
        return jwtDecoder;
    }

    @Bean
    public ApiKeyAuthFilter apiKeyAuthFilter() {
        return new ApiKeyAuthFilter(validApiKey, adminRoleId, writingRoleId, readingRoleId);
    }

    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {

        if ("test".equals(activeProfile)) {
            http.csrf(AbstractHttpConfigurer::disable)
                    .authorizeHttpRequests(request -> request.requestMatchers("/**").permitAll());
            return http.build();
        }

        JwtAuthenticationConverter jwtConverter = new CustomJwtAuthenticationConverter();
        http
                .cors(AbstractHttpConfigurer::disable)
                .csrf(AbstractHttpConfigurer::disable)
                .exceptionHandling(ex -> ex
                        .authenticationEntryPoint(new BearerTokenAuthenticationEntryPoint())
                        .accessDeniedHandler(new CustomAccessDeniedHandler(userPermissionDeniedMessage))
                )
                .addFilterBefore(apiKeyAuthFilter(), UsernamePasswordAuthenticationFilter.class)
                .authorizeHttpRequests(requests -> requests
                        .requestMatchers(HttpMethod.DELETE, "/**").hasAuthority(adminRoleId)
                        .requestMatchers(HttpMethod.PATCH, "/**").hasAnyAuthority(adminRoleId, writingRoleId)
                        .requestMatchers(HttpMethod.POST, "/**").hasAnyAuthority(adminRoleId, writingRoleId)
                        .requestMatchers(HttpMethod.GET, "/**").hasAnyAuthority(adminRoleId, writingRoleId, readingRoleId)
                        .anyRequest().authenticated()
                ).oauth2ResourceServer(oauth2ResourceServer ->
                        oauth2ResourceServer
                                .jwt(jwt -> jwt
                                        .jwtAuthenticationConverter(jwtConverter)
                                )
                );
        return http.build();
    }
}
