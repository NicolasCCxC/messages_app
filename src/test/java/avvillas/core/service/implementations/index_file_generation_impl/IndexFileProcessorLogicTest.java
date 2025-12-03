package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.constant.enums.LoadStatus;
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
import avvillas.core.web.traits.LoaderTrait;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.io.TempDir;
import org.mockito.ArgumentCaptor;
import org.mockito.Mockito;

import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;

import java.util.List;
import java.util.Collections;
import java.util.Objects;
import java.util.Optional;
import java.util.ArrayList;
import java.util.Map;
import java.util.Arrays;
import java.util.HashMap;

import static org.junit.jupiter.api.Assertions.assertTrue;
import static org.junit.jupiter.api.Assertions.assertEquals;

import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;
import static org.mockito.Mockito.any;
import static org.mockito.Mockito.atLeastOnce;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.eq;
import static org.mockito.Mockito.atLeast;
import static org.mockito.Mockito.anyString;

class IndexFileProcessorLogicTest {

    private final IndexFileRepository indexFileRepository = mock(IndexFileRepository.class);
    private final LoadFilesEntryRepository loadFilesEntryRepository = mock(LoadFilesEntryRepository.class);
    private final ProcessDataRepository processDataRepository = mock(ProcessDataRepository.class);
    private final EmailNotificationService emailNotificationService = mock(EmailNotificationService.class);
    private final LoaderTrait productTrait = mock(LoaderTrait.class);

    private IndexFileProcessorLogic buildSut() {
        IndexFileProcessorLogic sut = new IndexFileProcessorLogic(indexFileRepository, loadFilesEntryRepository, processDataRepository, emailNotificationService, productTrait);
        try {
            var f = IndexFileProcessorLogic.class.getDeclaredField("exitRoute");
            f.setAccessible(true);
            f.set(sut, "EXIT_ROUTE");
        } catch (Exception ignored) {
            // No relevant to the test
        }
        return sut;
    }

    @AfterEach
    void tearDown() {
        Mockito.reset(indexFileRepository, loadFilesEntryRepository, processDataRepository, emailNotificationService, productTrait);
    }

    @Test
    void whenLoadFilesEntryNotFound_thenSendsNotificationAndThrows_andSetsProgressToError() {
        IndexFileProcessorLogic sut = buildSut();
        String indexFileId = "IDX1";
        String productId = "P1";
        String period = "202401";
        PathExtractsArchiveIndexDto pathDto = new PathExtractsArchiveIndexDto();
        pathDto.setRouteOutputIndex(System.getProperty("java.io.tmpdir"));
        pathDto.setRouteOutputExtract(System.getProperty("java.io.tmpdir"));

        ContentIndexFileDto content = new ContentIndexFileDto();
        content.setNameIndexFile("index");
        content.setTypeFile(ContentIndexFileDto.TypeFile.CSV);
        content.setRequiredFields(Collections.emptyList());
        when(productTrait.getContentFileByProductId(eq(productId), any())).thenReturn(content);
        var productDto = new avvillas.core.service.dto.product.ProductDTO();
        productDto.setDescription("ProductoX");
        when(productTrait.getProductById(eq(productId), any())).thenReturn(productDto);

        when(loadFilesEntryRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(eq(productId), eq(period), eq(LoadStatus.FINALIZADO)))
                .thenReturn(null);

        IndexFileEntity idx = new IndexFileEntity();
        idx.setId(indexFileId);
        when(indexFileRepository.findById(indexFileId)).thenReturn(Optional.of(idx));
        when(indexFileRepository.save(any(IndexFileEntity.class))).thenAnswer(inv -> inv.getArgument(0));

        sut.process(indexFileId, productId, period, pathDto, 10, "token");

        verify(emailNotificationService, atLeastOnce()).sendProcessStatusNotification(eq("ProductoX"), eq("ERROR"), anyString(), any());

        ArgumentCaptor<IndexFileEntity> captor = ArgumentCaptor.forClass(IndexFileEntity.class);
        verify(indexFileRepository, atLeast(1)).save(captor.capture());
        boolean hasError = captor.getAllValues().stream().anyMatch(e -> e.getStatus() == LoadStatus.ERROR || Objects.equals(e.getPercentAdvance(), -1));
        assertTrue(hasError);
    }

    @Test
    void whenProcessDataEmpty_thenNotCreateFiles_andMarkFinalized(@TempDir Path tempDir) throws Exception {
        IndexFileProcessorLogic sut = buildSut();
        String indexFileId = "IDX2";
        String productId = "P2";
        String period = "202401";
        PathExtractsArchiveIndexDto pathDto = new PathExtractsArchiveIndexDto();
        pathDto.setRouteOutputIndex(tempDir.resolve("indices").toString());
        pathDto.setRouteOutputExtract(tempDir.toString());

        ContentIndexFileDto content = new ContentIndexFileDto();
        content.setNameIndexFile("index");
        content.setTypeFile(ContentIndexFileDto.TypeFile.CSV);
        content.setRequiredFields(Collections.emptyList());
        when(productTrait.getContentFileByProductId(eq(productId), any())).thenReturn(content);
        var productDto = new avvillas.core.service.dto.product.ProductDTO();
        productDto.setDescription("ProductoY");
        when(productTrait.getProductById(eq(productId), any())).thenReturn(productDto);

        LoadFilesEntryEntity load = new LoadFilesEntryEntity();
        load.setId("PROC-1");
        load.setClientsProcessed(0);
        when(loadFilesEntryRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(eq(productId), eq(period), eq(LoadStatus.FINALIZADO)))
                .thenReturn(load);

        when(processDataRepository.findByProcessId("PROC-1")).thenReturn(Optional.of(Collections.emptyList()));

        IndexFileEntity idx = new IndexFileEntity();
        idx.setId(indexFileId);
        when(indexFileRepository.findById(indexFileId)).thenReturn(Optional.of(idx));
        when(indexFileRepository.save(any(IndexFileEntity.class))).thenAnswer(inv -> inv.getArgument(0));

        // act
        sut.process(indexFileId, productId, period, pathDto, 10, "token");

        Path indices = tempDir.resolve("indices");
        assertTrue(Files.exists(indices));
        try (var stream = Files.list(indices)) {
            assertEquals(0L, stream.count());
        }
        ArgumentCaptor<IndexFileEntity> captor = ArgumentCaptor.forClass(IndexFileEntity.class);
        verify(indexFileRepository, atLeast(1)).save(captor.capture());
        boolean finalized = captor.getAllValues().stream().anyMatch(e -> e.getStatus() == LoadStatus.FINALIZADO && Objects.equals(e.getPercentAdvance(), 100));
        assertTrue(finalized);
    }

    @Test
    void happyPath_createsFiles_updatesFinalStatus(@TempDir Path tempDir) throws Exception {
        IndexFileProcessorLogic sut = buildSut();
        String indexFileId = "IDX3";
        String productId = "P3";
        String period = "202401";
        PathExtractsArchiveIndexDto pathDto = new PathExtractsArchiveIndexDto();
        pathDto.setRouteOutputIndex(tempDir.resolve("indices").toString());
        pathDto.setRouteOutputExtract(tempDir.resolve("extracts").toString());
        Files.createDirectories(tempDir.resolve("extracts"));

        ContentIndexFileDto content = new ContentIndexFileDto();
        content.setNameIndexFile("index");
        content.setTypeFile(ContentIndexFileDto.TypeFile.CSV);
        var fixed = new RequiredFieldsResDto();
        fixed.setIsFixed(true);
        fixed.setContent("FIXEDVAL");
        var exit = new RequiredFieldsResDto();
        exit.setIsFixed(true);
        exit.setContent("EXIT_ROUTE");
        content.setRequiredFields(Arrays.asList(fixed, exit));
        when(productTrait.getContentFileByProductId(eq(productId), any())).thenReturn(content);
        var productDto = new avvillas.core.service.dto.product.ProductDTO();
        productDto.setDescription("ProductoZ");
        when(productTrait.getProductById(eq(productId), any())).thenReturn(productDto);

        LoadFilesEntryEntity load = new LoadFilesEntryEntity();
        load.setId("PROC-2");
        load.setClientsProcessed(3);
        when(loadFilesEntryRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(eq(productId), eq(period), eq(LoadStatus.FINALIZADO)))
                .thenReturn(load);

        List<ProcessDataEntity> list = new ArrayList<>();
        for (int i = 1; i <= 3; i++) {
            ProcessDataEntity e = new ProcessDataEntity();
            e.setClientId("C" + i);
            Map<String, Object> map = new HashMap<>();
            map.put("fileName", "doc" + i);
            String json = new com.fasterxml.jackson.databind.ObjectMapper().writeValueAsString(map);
            e.setData(json.getBytes(StandardCharsets.UTF_8));
            list.add(e);
        }
        when(processDataRepository.findByProcessId("PROC-2")).thenReturn(Optional.of(list));

        IndexFileEntity idx = new IndexFileEntity();
        idx.setId(indexFileId);
        when(indexFileRepository.findById(indexFileId)).thenReturn(Optional.of(idx));
        when(indexFileRepository.save(any(IndexFileEntity.class))).thenAnswer(inv -> inv.getArgument(0));
        when(indexFileRepository.findById("IDX3")).thenReturn(Optional.of(idx));

        sut.process(indexFileId, productId, period, pathDto, 2, "token");

        Path indices = tempDir.resolve("indices");
        assertTrue(Files.exists(indices));
        try (var stream = Files.list(indices)) {
            List<Path> files = stream.sorted().toList();
            assertEquals(2, files.size());
            List<String> f1 = Files.readAllLines(files.get(0));
            List<String> f2 = Files.readAllLines(files.get(1));
            assertTrue(f1.size() + f2.size() == 3);
            assertTrue(f1.stream().allMatch(s -> s.contains("FIXEDVAL")));
        }

        verify(emailNotificationService, atLeastOnce()).sendProcessStatusNotification(eq("ProductoZ"), eq("FINALIZADO"), anyString(), any());

        ArgumentCaptor<IndexFileEntity> captor = ArgumentCaptor.forClass(IndexFileEntity.class);
        verify(indexFileRepository, atLeast(1)).save(captor.capture());
        boolean finalized = captor.getAllValues().stream().anyMatch(e -> e.getStatus() == LoadStatus.FINALIZADO && Objects.equals(e.getPercentAdvance(), 100) && Objects.equals(e.getProcessId(), "PROC-2") && Objects.equals(e.getClientsProcessed(), 3));
        assertTrue(finalized);
    }

    @Test
    void whenTotalsMismatch_thenErrorAndProgressMinusOne(@TempDir Path tempDir) {
        IndexFileProcessorLogic sut = buildSut();
        String indexFileId = "IDX4";
        String productId = "P4";
        String period = "202401";
        PathExtractsArchiveIndexDto pathDto = new PathExtractsArchiveIndexDto();
        pathDto.setRouteOutputIndex(tempDir.resolve("indices").toString());
        pathDto.setRouteOutputExtract(tempDir.toString());

        ContentIndexFileDto content = new ContentIndexFileDto();
        content.setNameIndexFile("index");
        content.setTypeFile(ContentIndexFileDto.TypeFile.CSV);
        content.setRequiredFields(Collections.emptyList());
        when(productTrait.getContentFileByProductId(eq(productId), any())).thenReturn(content);
        var productDto = new avvillas.core.service.dto.product.ProductDTO();
        productDto.setDescription("ProductoW");
        when(productTrait.getProductById(eq(productId), any())).thenReturn(productDto);

        LoadFilesEntryEntity load = new LoadFilesEntryEntity();
        load.setId("PROC-3");
        load.setClientsProcessed(5);
        when(loadFilesEntryRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(eq(productId), eq(period), eq(LoadStatus.FINALIZADO)))
                .thenReturn(load);

        List<ProcessDataEntity> list = new ArrayList<>();
        for (int i = 1; i <= 3; i++) {
            ProcessDataEntity e = new ProcessDataEntity();
            e.setClientId("C" + i);
            e.setData("{}".getBytes(StandardCharsets.UTF_8));
            list.add(e);
        }
        when(processDataRepository.findByProcessId("PROC-3")).thenReturn(Optional.of(list));

        IndexFileEntity idx = new IndexFileEntity();
        idx.setId(indexFileId);
        when(indexFileRepository.findById(indexFileId)).thenReturn(Optional.of(idx));
        when(indexFileRepository.save(any(IndexFileEntity.class))).thenAnswer(inv -> inv.getArgument(0));

        sut.process(indexFileId, productId, period, pathDto, 10, "token");

        verify(emailNotificationService, atLeastOnce()).sendProcessStatusNotification(eq("ProductoW"), eq("ERROR"), anyString(), any());
        ArgumentCaptor<IndexFileEntity> captor = ArgumentCaptor.forClass(IndexFileEntity.class);
        verify(indexFileRepository, atLeast(1)).save(captor.capture());
        boolean error = captor.getAllValues().stream().anyMatch(e -> e.getStatus() == LoadStatus.ERROR || Objects.equals(e.getPercentAdvance(), -1));
        assertTrue(error);
    }
}