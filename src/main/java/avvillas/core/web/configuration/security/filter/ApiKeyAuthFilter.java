package avvillas.core.web.configuration.security.filter;

import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.filter.OncePerRequestFilter;

import java.io.IOException;
import java.util.List;

public class ApiKeyAuthFilter extends OncePerRequestFilter {

    private final String validApiKey;
    private final String adminRoleId;
    private final String writingRoleId;
    private final String readingRoleId;

    public ApiKeyAuthFilter(String validApiKey, String adminRoleId, String writingRoleId, String readingRoleId) {
        this.validApiKey = validApiKey;
        this.adminRoleId = adminRoleId;
        this.writingRoleId = writingRoleId;
        this.readingRoleId = readingRoleId;
    }

    @Override
    protected void doFilterInternal(HttpServletRequest request, HttpServletResponse response, FilterChain filterChain)
            throws ServletException, IOException {

        if (SecurityContextHolder.getContext().getAuthentication() != null &&
                SecurityContextHolder.getContext().getAuthentication().isAuthenticated()) {
            filterChain.doFilter(request, response);
            return;
        }

        String apiKey = request.getHeader("API-KEY");

        if (apiKey != null && apiKey.equals(validApiKey)) {
            List<SimpleGrantedAuthority> authorities = List.of(
                    new SimpleGrantedAuthority(adminRoleId),
                    new SimpleGrantedAuthority(writingRoleId),
                    new SimpleGrantedAuthority(readingRoleId)
            );

            UsernamePasswordAuthenticationToken authentication =
                    new UsernamePasswordAuthenticationToken("API_USER", null, authorities);

            SecurityContextHolder.getContext().setAuthentication(authentication);
        }
        filterChain.doFilter(request, response);
    }
}