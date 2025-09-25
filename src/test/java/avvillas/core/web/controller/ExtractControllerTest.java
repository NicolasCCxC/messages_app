package avvillas.core.web.controller;

import avvillas.core.constant.message.ExtractMessage;
import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.service.dto.extract.ExtractDto;
import avvillas.core.service.extract_generation.ExtractOrchestratorService;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.Mockito;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.http.MediaType;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.eq;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.jsonPath;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;

@SpringBootTest
@ActiveProfiles("test")
@AutoConfigureMockMvc
class ExtractControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @Autowired
    private ObjectMapper objectMapper;

    @MockitoBean
    private ExtractOrchestratorService extractOrchestratorService;

    private ExtractDto requestDto;
    private ExtractDto responseDto;

    @BeforeEach
    void setup() {
        // Ensure there is an authenticated user in the SecurityContext for controller method param Authentication
        SecurityContextHolder.getContext().setAuthentication(
                new UsernamePasswordAuthenticationToken("user-123", null, null)
        );

        requestDto = new ExtractDto();
        requestDto.setProductId("4edcdd5a-a5b0-4bf5-88c1-735e81cdbe25");
        requestDto.setPeriod("20240731");

        responseDto = new ExtractDto();
        responseDto.setProductId(requestDto.getProductId());
        responseDto.setPeriod(requestDto.getPeriod());
        responseDto.setStatus("IN_PROGRESS");
        responseDto.setPercentAdvance("0");
        responseDto.setUser("user-123");
    }

    @Test
    @DisplayName("Debería generar extractos correctamente (POST /extract/generate)")
    void shouldGenerateExtracts() throws Exception {
        Mockito.when(extractOrchestratorService.generateExtracts(any(ExtractDto.class), eq("user-123")))
                .thenReturn(responseDto);

        String json = objectMapper.writeValueAsString(requestDto);

        mockMvc.perform(post("/extract/generate")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(json))
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.message[0]").value(ExtractMessage.EXTRACT_PROCESS))
                .andExpect(jsonPath("$.data.productId").value(responseDto.getProductId()))
                .andExpect(jsonPath("$.data.period").value(responseDto.getPeriod()))
                .andExpect(jsonPath("$.data.status").value("IN_PROGRESS"));

        Mockito.verify(extractOrchestratorService).generateExtracts(any(ExtractDto.class), eq("user-123"));
    }

    @Test
    @DisplayName("Debería retornar 400 cuando el body es inválido (validaciones @Valid)")
    void shouldReturnBadRequestOnValidationErrors() throws Exception {
        ExtractDto invalid = new ExtractDto();
        invalid.setProductId("not-a-uuid"); // inválido
        invalid.setPeriod("2024"); // inválido, longitud 8

        String json = objectMapper.writeValueAsString(invalid);

        mockMvc.perform(post("/extract/generate")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(json))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.data").doesNotExist())
                .andExpect(jsonPath("$.message").isArray())
                .andExpect(jsonPath("$.message").isNotEmpty())
                .andExpect(jsonPath("$.message[*]").value(org.hamcrest.Matchers.hasItems(
                        IndexFileMessage.PRODUCT_ID_INVALID,
                        IndexFileMessage.PERIOD_MAX
                )));

        Mockito.verifyNoInteractions(extractOrchestratorService);
    }
}
