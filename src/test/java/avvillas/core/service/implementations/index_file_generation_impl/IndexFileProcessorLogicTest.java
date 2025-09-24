package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.entity.LoadFilesEntryEntity;
import avvillas.core.persistence.entity.ProcessDataEntity;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.persistence.repository.LoadFilesEntryRepository;
import avvillas.core.persistence.repository.ProcessDataRepository;
import avvillas.core.service.EmailNotificationService;
import avvillas.core.service.dto.content_index_file.ContentIndexFileDto;
import avvillas.core.service.dto.content_index_file.RequiredFieldsResDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.web.traits.LoaderTrait;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.test.util.ReflectionTestUtils;

import java.nio.file.Files;
import java.util.List;
import java.util.Optional;

import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;
import static org.mockito.Mockito.any;

class IndexFileProcessorLogicTest {

    private IndexFileProcessorLogic processor;
    private IndexFileRepository indexFileRepository;
    private LoadFilesEntryRepository loadFilesEntryRepository;
    private ProcessDataRepository processDataRepository;
    private EmailNotificationService emailNotificationService;
    private LoaderTrait loaderTrait;

    @BeforeEach
    void setUp() {
        indexFileRepository = mock(IndexFileRepository.class);
        loadFilesEntryRepository = mock(LoadFilesEntryRepository.class);
        processDataRepository = mock(ProcessDataRepository.class);
        emailNotificationService = mock(EmailNotificationService.class);
        loaderTrait = mock(LoaderTrait.class);

        processor = new IndexFileProcessorLogic(
                indexFileRepository, loadFilesEntryRepository, processDataRepository,
                emailNotificationService, loaderTrait
        );

        ReflectionTestUtils.setField(processor, "exitRoute", "EXIT");
    }

    @Test
    void givenValidData_whenProcess_thenUpdatesProgress() throws Exception {
        ProductDTO productDTO = new ProductDTO();
        productDTO.setId("P1");
        productDTO.setCode("CODE1");
        productDTO.setDescription("Producto de prueba");
        ContentIndexFileDto content = new ContentIndexFileDto();
        content.setNameIndexFile("idx");
        content.setTypeFile(ContentIndexFileDto.TypeFile.CSV);
        content.setRequiredFields(List.of(new RequiredFieldsResDto()));
        when(loaderTrait.getContentFileByProductId(any(), any())).thenReturn(content);
        when(loaderTrait.getProductById(any(), any())).thenReturn(productDTO);

        LoadFilesEntryEntity loadEntity = new LoadFilesEntryEntity();
        loadEntity.setId("proc-2");
        loadEntity.setClientsProcessed(1);
        when(loadFilesEntryRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(any(), any(), any()))
                .thenReturn(loadEntity);

        ProcessDataEntity data = new ProcessDataEntity();
        data.setClientId("c1");
        data.setData("{\"fileName\":\"doc1\"}".getBytes());
        when(processDataRepository.findByProcessId("proc-2")).thenReturn(Optional.of(List.of(data)));

        IndexFileEntity entity = new IndexFileEntity();
        entity.setId("id-1");
        when(indexFileRepository.findById("id-1")).thenReturn(Optional.of(entity));

        PathExtractsArchiveIndexDto path = new PathExtractsArchiveIndexDto();
        path.setRouteOutputIndex(Files.createTempDirectory("test-idx3").toString());
        path.setRouteOutputExtract("/tmp");
    }
}