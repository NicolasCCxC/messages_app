package avvillas.core.web.configuration;

import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.web.access.AccessDeniedHandler;

import java.io.IOException;

public class CustomAccessDeniedHandler implements AccessDeniedHandler {
    private final String permissionDenied;

    @Autowired
    public CustomAccessDeniedHandler(String permissionDenied) {
        this.permissionDenied = permissionDenied;
    }

    @Override
    public void handle(
            HttpServletRequest request,
            HttpServletResponse response,
            AccessDeniedException exc) throws IOException {
        response.setContentType("application/json");
        response.setStatus(HttpServletResponse.SC_FORBIDDEN);
        response.getOutputStream().println(generateErrorJson(permissionDenied));
    }

    private String generateErrorJson(String message) {
        return String.format("{ \"error\": \"%s\" }", message);
    }
}