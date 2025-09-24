package avvillas.core.web.controller.index_file;

import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.service.IndexFileService;
import avvillas.core.service.dto.index_file.IndexDto;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.Mockito;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.http.MediaType;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.jsonPath;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;

import static org.mockito.ArgumentMatchers.any;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;

@SpringBootTest
@ActiveProfiles("test")
@AutoConfigureMockMvc
class IndexFileControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @Autowired
    protected ObjectMapper objectMapper;

    @Autowired
    private IndexFileRepository indexFileRepository;

    @MockitoBean
    private IndexFileService indexFileService;

    private IndexDto indexDto;

    @BeforeEach
    void setUp() {
        indexDto = new IndexDto();
        indexDto.setProductId("4edcdd5a-a5b0-4bf5-88c1-735e81cdbe25");
        indexDto.setPeriod("20240731");
    }

    @Test
    @DisplayName("Debería generar un index file correctamente (POST /index/file/generate)")
    void shouldGenerateIndexFile() throws Exception {
        Mockito.when(indexFileService.generateIndexFile(any(IndexDto.class)))
                .thenReturn(indexDto);

        String requestJson = """
                {
                    "id": "%s",
                    "productId": "4edcdd5a-a5b0-4bf5-88c1-735e81cdbe25",
                    "period": "20240731"
                }
                """.formatted("8a589718-ebb8-4bf1-857a-bd1be984aa0c");

        mockMvc.perform(post("/index/file/generate")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(requestJson))
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.message[0]").value(IndexFileMessage.INDEX_FILE_PROCESS));
    }
}