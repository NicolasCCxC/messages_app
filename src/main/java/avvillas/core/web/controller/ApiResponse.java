package avvillas.core.web.controller;

import lombok.Getter;

import java.util.Collections;
import java.util.List;

@Getter
public class ApiResponse<T> {
    private final T data;
    private final List<String> message;
    private final String service = "CORE";

    private ApiResponse(T data, List<String> message) {
        this.data = data;
        this.message = message;
    }

    public static <T> ApiResponse<T> success(T data, String message) {
        return new ApiResponse<>(
                data,
                Collections.singletonList(message)
        );
    }


    public static <T> ApiResponse<T> error(List<String> errorMessages) {
        return new ApiResponse<>(
                null,
                errorMessages
        );
    }

    public static <T> ApiResponse<T> error(String errorMessage) {
        return new ApiResponse<>(
                null,
                Collections.singletonList(errorMessage)
        );
    }
}