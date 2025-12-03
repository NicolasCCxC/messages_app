package avvillas.core.web.controller.configuration.security.filter;

import avvillas.core.web.configuration.security.filter.InjectionFilter;
import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.ServletRequest;
import jakarta.servlet.ServletResponse;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.context.ActiveProfiles;

import java.io.IOException;

import static org.mockito.Mockito.*;

@ActiveProfiles("test")
@SpringBootTest
@AutoConfigureMockMvc
@TestInstance(TestInstance.Lifecycle.PER_CLASS)
class InjectionFilterTest {

    private InjectionFilter injectionFilter;
    private FilterChain chain;
    private ServletRequest invalidRequest;
    private ServletResponse response;

    @BeforeEach
    void setUp() {
        injectionFilter = new InjectionFilter();
        chain = mock(FilterChain.class);
        invalidRequest = mock(ServletRequest.class);
        response = mock(ServletResponse.class);
    }

    @Test
    void testDoFilter_withNonHttpServletRequest() throws ServletException, IOException {
        injectionFilter.doFilter(invalidRequest, response, chain);

        verify(chain, times(1)).doFilter(invalidRequest, response);
        verifyNoMoreInteractions(chain);
    }

    @Test
    void testDestroyIsCalled() {
        InjectionFilter customFilter = spy(new InjectionFilter());

        customFilter.destroy();

        verify(customFilter, times(1)).destroy();
    }

}