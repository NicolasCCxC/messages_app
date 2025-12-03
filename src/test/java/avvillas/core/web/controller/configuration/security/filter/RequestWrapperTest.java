package avvillas.core.web.controller.configuration.security.filter;

import avvillas.core.web.configuration.security.filter.RequestWrapper;
import jakarta.servlet.http.HttpServletRequest;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.context.ActiveProfiles;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertFalse;
import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;

@ActiveProfiles("test")
@SpringBootTest
@AutoConfigureMockMvc
@TestInstance(TestInstance.Lifecycle.PER_CLASS)
class RequestWrapperTest {

    private HttpServletRequest mockRequest;
    private RequestWrapper requestWrapper;

    @BeforeEach
    void setUp() throws Exception {
        mockRequest = mock(HttpServletRequest.class);
        String requestBody = "";
        InputStream inputStream = new ByteArrayInputStream(requestBody.getBytes());
        when(mockRequest.getInputStream()).thenReturn(new MockServletInputStream(inputStream));
        when(mockRequest.getContentType()).thenReturn("text/plain");

        requestWrapper = new RequestWrapper(mockRequest);
    }

    @Test
    void testGetReader() throws Exception {
        String requestBody = "Sample request body";
        InputStream inputStream = new ByteArrayInputStream(requestBody.getBytes());
        when(mockRequest.getInputStream()).thenReturn(new MockServletInputStream(inputStream));

        RequestWrapper requestWrapperReader = new RequestWrapper(mockRequest);

        BufferedReader reader = requestWrapperReader.getReader();
        String result = reader.readLine();

        assertEquals(requestBody, result);
    }

    private List<Object> invokeSanitizeList(List<Object> list) throws Exception {
        Method sanitizeListMethod = RequestWrapper.class.getDeclaredMethod("sanitizeList", List.class);
        sanitizeListMethod.setAccessible(true);
        return (List<Object>) sanitizeListMethod.invoke(requestWrapper, list);
    }

    @Test
    void testSanitizeListWithNullValues() throws Exception {
        List<Object> inputList = new ArrayList<>();
        inputList.add("test");
        inputList.add(null);
        inputList.add("another test");
        inputList.add(null);

        List<Object> result = invokeSanitizeList(inputList);

        assertEquals(2, result.size());
        assertEquals("test", result.get(0));
        assertEquals("another test", result.get(1));
        assertFalse(result.contains(null));
    }

    @Test
    void testSanitizeListWithStringValues() throws Exception {
        List<Object> inputList = new ArrayList<>();
        inputList.add("normal text");
        inputList.add("<script>alert('XSS')</script>");
        inputList.add("text with <tags>");

        List<Object> result = invokeSanitizeList(inputList);

        assertEquals(3, result.size());
        assertEquals("normal text", result.get(0));
        assertEquals("&lt;script&gt;alert(&#x27;XSS&#x27;)&lt;/script&gt;", result.get(1)); // Malicious content is HTML-encoded
        assertEquals("text with &lt;tags&gt;", result.get(2));
    }

    @Test
    void testSanitizeListWithMapValues() throws Exception {
        List<Object> inputList = new ArrayList<>();
        Map<String, Object> map1 = new HashMap<>();
        map1.put("key1", "value1");
        map1.put("key2", "<script>alert('XSS')</script>");

        Map<String, Object> map2 = new HashMap<>();
        map2.put("key3", "value3");
        map2.put("key4", null);

        inputList.add(map1);
        inputList.add(map2);

        List<Object> result = invokeSanitizeList(inputList);

        assertEquals(2, result.size());

        Map<String, Object> resultMap1 = (Map<String, Object>) result.get(0);
        assertEquals("value1", resultMap1.get("key1"));
        assertEquals("&lt;script&gt;alert(&#x27;XSS&#x27;)&lt;/script&gt;", resultMap1.get("key2")); // Malicious content is HTML-encoded

        Map<String, Object> resultMap2 = (Map<String, Object>) result.get(1);
        assertEquals("value3", resultMap2.get("key3"));
        assertFalse(resultMap2.containsKey("key4"));
    }

    @Test
    void testSanitizeListWithNestedListValues() throws Exception {
        List<Object> inputList = new ArrayList<>();
        List<Object> nestedList = new ArrayList<>();
        nestedList.add("nested item 1");
        nestedList.add("<script>alert('XSS')</script>");
        nestedList.add(null);

        inputList.add("top level item");
        inputList.add(nestedList);

        List<Object> result = invokeSanitizeList(inputList);

        assertEquals(2, result.size());
        assertEquals("top level item", result.get(0));

        List<Object> resultNestedList = (List<Object>) result.get(1);
        assertEquals(2, resultNestedList.size());
        assertEquals("nested item 1", resultNestedList.get(0));
        assertEquals("&lt;script&gt;alert(&#x27;XSS&#x27;)&lt;/script&gt;", resultNestedList.get(1)); // Malicious content is HTML-encoded
        assertFalse(resultNestedList.contains(null));
    }

    @Test
    void testSanitizeListWithMixedContentTypes() throws Exception {
        List<Object> inputList = new ArrayList<>();
        inputList.add("string value");
        inputList.add(123);
        inputList.add(true);

        Map<String, Object> map = new HashMap<>();
        map.put("key", "<img src=x onerror=alert('XSS')>");
        inputList.add(map);

        List<Object> nestedList = new ArrayList<>();
        nestedList.add("nested value");
        inputList.add(nestedList);

        List<Object> result = invokeSanitizeList(inputList);

        assertEquals(5, result.size());
        assertEquals("string value", result.get(0));
        assertEquals(123, result.get(1));
        assertEquals(true, result.get(2));

        Map<String, Object> resultMap = (Map<String, Object>) result.get(3);
        assertEquals("", resultMap.get("key"));

        List<Object> resultNestedList = (List<Object>) result.get(4);
        assertEquals(1, resultNestedList.size());
        assertEquals("nested value", resultNestedList.get(0));
    }
}