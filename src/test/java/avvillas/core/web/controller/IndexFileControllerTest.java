package avvillas.core.web.controller;

import avvillas.core.constant.message.CommonMessage;
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
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageImpl;
import org.springframework.data.domain.PageRequest;
import org.springframework.http.MediaType;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import java.util.List;

import static org.mockito.ArgumentMatchers.eq;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.jsonPath;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;

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

    @Test
    @DisplayName("Debería listar index files con paginación y mensaje OK (GET /index)")
    void shouldListIndexFilesWithPagination() throws Exception {
        IndexDto dto1 = new IndexDto();
        dto1.setProductId("4edcdd5a-a5b0-4bf5-88c1-735e81cdbe25");
        dto1.setPeriod("20240731");

        IndexDto dto2 = new IndexDto();
        dto2.setProductId("6edcdd5a-a5b0-4bf5-88c1-735e81cdbe26");
        dto2.setPeriod("20240831");

        Page<IndexDto> page = new PageImpl<>(List.of(dto1, dto2), PageRequest.of(0, 10), 2);

        Mockito.when(indexFileService.searchGlobal(eq("abc"), eq(PageRequest.of(0, 10))))
                .thenReturn(page);

        mockMvc.perform(get("/index")
                        .param("search", "abc")
                        .param("page", "0")
                        .param("size", "10")
                        .accept(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.message[0]").value(CommonMessage.QUERY_SUCCESS))
                .andExpect(jsonPath("$.data.content[0].productId").value("4edcdd5a-a5b0-4bf5-88c1-735e81cdbe25"))
                .andExpect(jsonPath("$.data.content[1].productId").value("6edcdd5a-a5b0-4bf5-88c1-735e81cdbe26"))
                .andExpect(jsonPath("$.data.size").value(10))
                .andExpect(jsonPath("$.data.number").value(0))
                .andExpect(jsonPath("$.data.totalElements").value(2));
    }

    @Test
    @DisplayName("Debería usar valores por defecto de paginación cuando no se envían parámetros (GET /index)")
    void shouldUseDefaultPaginationWhenNoParams() throws Exception {
        Page<IndexDto> emptyPage = Page.empty(PageRequest.of(0, 10));
        Mockito.when(indexFileService.searchGlobal(eq(null), eq(PageRequest.of(0, 10))))
                .thenReturn(emptyPage);

        mockMvc.perform(get("/index").accept(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.message[0]").value(CommonMessage.QUERY_SUCCESS))
                .andExpect(jsonPath("$.data.content").isArray());
    }
}