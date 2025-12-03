package avvillas.core.web.configuration.security.filter;

import com.fasterxml.jackson.databind.ObjectMapper;
import jakarta.servlet.ReadListener;
import jakarta.servlet.ServletInputStream;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletRequestWrapper;

import java.io.*;
import java.util.*;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.stream.Collectors;

public class RequestWrapper extends HttpServletRequestWrapper {
    private final String body;
    private static final ObjectMapper objectMapper = new ObjectMapper();
    private static final Set<String> MALICIOUS_PATTERNS = Set.of(
            "<script", "javascript:", "vbscript:",
            "onload=", "onerror=", "onclick=",
            "alert(", "eval(", "document.cookie",
            "union select", "drop table", "--",
            "' or '1'='1", "\" or \"1\"=\"1",
            "exec(", "execute(", "WAITFOR DELAY",
            "declare @", "'; exec", "'; declare",
            "../../", "../", "..\\", "\\\\",
            "; declare", "; exec", "; select",
            "/*", "*/", "@@"
    );
    private static final Map<String, String> HTML_ENTITIES = new HashMap<>();
    private static final Pattern ROUTE_PATTERN = Pattern.compile("([<>\"'])");
    private static final Pattern DEFAULT_PATTERN = Pattern.compile("([<>\"'/\\\\])");

    static {
        HTML_ENTITIES.put("<", "&lt;");
        HTML_ENTITIES.put(">", "&gt;");
        HTML_ENTITIES.put("\"", "&quot;");
        HTML_ENTITIES.put("'", "&#x27;");
        HTML_ENTITIES.put("/", "&#x2F;");
        HTML_ENTITIES.put("\\", "&#x5C;");
    }

    public RequestWrapper(HttpServletRequest request) throws IOException {
        super(request);
        String requestBody = getRequestBody(request);
        this.body = isJsonRequest(request) ?
                sanitizeJsonBody(requestBody) :
                sanitizeInput(requestBody);
    }

    private String getRequestBody(HttpServletRequest request) throws IOException {
        StringBuilder stringBuilder = new StringBuilder();
        try (InputStream inputStream = request.getInputStream();
             BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(inputStream))) {

            char[] charBuffer = new char[128];
            int bytesRead;
            while ((bytesRead = bufferedReader.read(charBuffer)) > 0) {
                stringBuilder.append(charBuffer, 0, bytesRead);
            }
        }
        return stringBuilder.toString();
    }

    private boolean isJsonRequest(HttpServletRequest request) {
        return Optional.ofNullable(request.getContentType())
                .map(String::toLowerCase)
                .filter(contentType -> contentType.contains("application/json"))
                .isPresent();
    }

    @Override
    public ServletInputStream getInputStream() throws IOException {
        final ByteArrayInputStream byteArrayInputStream = new ByteArrayInputStream(body.getBytes());

        return new ServletInputStream() {
            @Override
            public boolean isFinished() {
                return byteArrayInputStream.available() == 0;
            }

            @Override
            public boolean isReady() {
                return true;
            }

            @Override
            public void setReadListener(ReadListener readListener) {
                throw new UnsupportedOperationException("setReadListener no soportado");
            }

            @Override
            public int read() throws IOException {
                return byteArrayInputStream.read();
            }
        };
    }

    @Override
    public BufferedReader getReader() throws IOException {
        return new BufferedReader(new InputStreamReader(this.getInputStream()));
    }

    @Override
    public String getParameter(String name) {
        return Optional.ofNullable(super.getParameter(name))
                .map(this::sanitizeInput)
                .orElse(null);
    }

    @Override
    public Map<String, String[]> getParameterMap() {
        Map<String, String[]> originalMap = super.getParameterMap();
        Map<String, String[]> sanitizedMap = new HashMap<>();

        originalMap.forEach((key, values) -> {
            String[] sanitizedValues = new String[values.length];
            for (int i = 0; i < values.length; i++) {
                sanitizedValues[i] = sanitizeInput(values[i]);
            }
            sanitizedMap.put(sanitizeInput(key), sanitizedValues);
        });

        return Map.copyOf(sanitizedMap);
    }

    private String sanitizeJsonBody(String jsonBody) {
        try {
            if (jsonBody == null || jsonBody.isEmpty()) {
                return jsonBody;
            }

            Map<String, Object> jsonMap = objectMapper.readValue(jsonBody, Map.class);
            Map<String, Object> sanitizedMap = sanitizeMap(jsonMap);

            return objectMapper.writeValueAsString(sanitizedMap);
        } catch (IOException e) {
            return jsonBody;
        }
    }

    private Map<String, Object> sanitizeMap(Map<String, Object> map) {
        return map.entrySet().stream()
                .filter(entry -> entry.getValue() != null)
                .collect(Collectors.toMap(
                        Map.Entry::getKey,
                        entry -> {
                            Object value = entry.getValue();
                            if (value instanceof String) {
                                return sanitizeInput((String) value);
                            } else if (value instanceof Map) {
                                return sanitizeMap((Map<String, Object>) value);
                            } else if (value instanceof List) {
                                return sanitizeList((List<Object>) value);
                            } else {
                                return value;
                            }
                        }
                ));
    }

    private List<Object> sanitizeList(List<Object> list) {
        return list.stream()
                .filter(Objects::nonNull)
                .map(item -> {
                    if (item instanceof String) {
                        return sanitizeInput((String) item);
                    } else if (item instanceof Map) {
                        return sanitizeMap((Map<String, Object>) item);
                    } else if (item instanceof List) {
                        return sanitizeList((List<Object>) item);
                    } else {
                        return item;
                    }
                })
                .toList();
    }

    private String sanitizeInput(String input) {
        if (input == null) return null;

        if (isRoute(input)) {
            return replaceUsingPattern(input, ROUTE_PATTERN);
        }

        return containsMaliciousContent(input)
                ? ""
                : replaceUsingPattern(input, DEFAULT_PATTERN);
    }

    private String replaceUsingPattern(String input, Pattern pattern) {
        Matcher matcher = pattern.matcher(input);
        StringBuffer sb = new StringBuffer();

        while (matcher.find()) {
            String replacement = HTML_ENTITIES.getOrDefault(matcher.group(1), matcher.group(1));
            matcher.appendReplacement(sb, Matcher.quoteReplacement(replacement));
        }
        matcher.appendTail(sb);

        return sb.toString();
    }

    private boolean isRoute(String input) {
        return input.contains("/") || input.contains("\\");
    }

    private boolean containsMaliciousContent(String input) {
        if (input == null) return false;
        String lowerInput = input.toLowerCase();
        return MALICIOUS_PATTERNS.stream().anyMatch(lowerInput::contains);
    }
}