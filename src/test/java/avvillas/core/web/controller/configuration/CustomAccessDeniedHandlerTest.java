package avvillas.core.web.controller.configuration;

import avvillas.core.web.configuration.CustomAccessDeniedHandler;
import jakarta.servlet.ServletOutputStream;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.mock.web.DelegatingServletOutputStream;
import org.springframework.security.access.AccessDeniedException;
import org.springframework.test.context.ActiveProfiles;

import java.io.ByteArrayOutputStream;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.mockito.Mockito.*;

@ActiveProfiles("test")
@SpringBootTest
@AutoConfigureMockMvc
@TestInstance(TestInstance.Lifecycle.PER_CLASS)
class CustomAccessDeniedHandlerTest {

    @Test
    @DisplayName("Should handle AccessDeniedException and return 403 with JSON error message")
    void testHandleReturnsForbiddenResponse() throws Exception {
        ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();
        ServletOutputStream servletOutputStream = new DelegatingServletOutputStream(byteArrayOutputStream);

        HttpServletResponse mockResponse = mock(HttpServletResponse.class);
        when(mockResponse.getOutputStream()).thenReturn(servletOutputStream);

        HttpServletRequest mockRequest = mock(HttpServletRequest.class);
        AccessDeniedException mockException = mock(AccessDeniedException.class);

        String expectedMessage = "Permission Denied";
        CustomAccessDeniedHandler accessDeniedHandler = new CustomAccessDeniedHandler(expectedMessage);

        accessDeniedHandler.handle(mockRequest, mockResponse, mockException);

        verify(mockResponse).setContentType("application/json");
        verify(mockResponse).setStatus(HttpServletResponse.SC_FORBIDDEN);

        String responseContent = byteArrayOutputStream.toString().trim();
        String expectedJson = "{ \"error\": \"" + expectedMessage + "\" }";
        assertEquals(expectedJson, responseContent);
    }

}