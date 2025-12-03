package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.persistence.entity.ProcessDataEntity;
import avvillas.core.persistence.repository.ProcessDataRepository;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties.Performance;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageImpl;
import org.springframework.data.domain.PageRequest;

import java.nio.charset.StandardCharsets;

import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.concurrent.Executor;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.concurrent.atomic.AtomicBoolean;

import static org.assertj.core.api.Assertions.assertThat;

import static org.mockito.Mockito.when;
import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.eq;
import static org.mockito.Mockito.any;
import static org.mockito.Mockito.verifyNoInteractions;

class ClientDataServiceImplTest {

    private ProcessDataRepository repository;
    private ObjectMapper objectMapper;
    private ClientDataServiceImpl service;

    @BeforeEach
    void setUp() {
        repository = mock(ProcessDataRepository.class);
        objectMapper = new ObjectMapper();
        Executor executor = Runnable::run;

        ExtractProperties extractProperties = mock(ExtractProperties.class);
        Performance performance = new Performance();
        ExtractProperties.ProducerPool pool = new ExtractProperties.ProducerPool();
        pool.setIdBatchSize(2);
        performance.setProducerPool(pool);
        when(extractProperties.getPerformance()).thenReturn(performance);

        service = new ClientDataServiceImpl(repository, objectMapper, executor, extractProperties);
    }

    @Test
    void produceClientData_shouldEnqueueClientDataSuccessfully() throws Exception {
        String processId = "proc1";

        List<String> clientIds = List.of("c1");
        Page<String> page = new PageImpl<>(clientIds, PageRequest.of(0, 2), 1);

        when(repository.findClientIdsByProcessId(eq(processId), any())).thenReturn(page);
        Map<String, Object> fakeJson = Map.of("name", "John");
        byte[] data = objectMapper.writeValueAsBytes(fakeJson);

        ProcessDataEntity entity = new ProcessDataEntity();
        entity.setData(data);

        when(repository.findById(any())).thenReturn(Optional.of(entity));

        BlockingQueue<Map<String, Object>> queue = new LinkedBlockingQueue<>();
        AtomicBoolean failed = new AtomicBoolean(false);

        service.produceClientData(processId, queue, 1, failed);

        Map<String, Object> first = queue.poll(1, TimeUnit.SECONDS);
        assertThat(first).containsEntry("name", "John");

        Map<String, Object> endSignal = queue.poll(1, TimeUnit.SECONDS);
        assertThat(endSignal).isEqualTo(ClientDataServiceImpl.END_OF_QUEUE);
    }

    @Test
    void produceClientData_shouldStopWhenProcessFailedFlagIsTrue() throws Exception {
        String processId = "proc2";

        BlockingQueue<Map<String, Object>> queue = new LinkedBlockingQueue<>();
        AtomicBoolean failed = new AtomicBoolean(true);

        service.produceClientData(processId, queue, 2, failed);

        Map<String, Object> end1 = queue.poll(1, TimeUnit.SECONDS);
        Map<String, Object> end2 = queue.poll(1, TimeUnit.SECONDS);

        assertThat(end1).isEqualTo(ClientDataServiceImpl.END_OF_QUEUE);
        assertThat(end2).isEqualTo(ClientDataServiceImpl.END_OF_QUEUE);

        verifyNoInteractions(repository);
    }

    @Test
    void produceClientData_shouldHandleDeserializationErrorGracefully() throws Exception {
        String processId = "proc3";

        List<String> clientIds = List.of("c1");
        Page<String> page = new PageImpl<>(clientIds, PageRequest.of(0, 2), 1);

        when(repository.findClientIdsByProcessId(eq(processId), any())).thenReturn(page);

        ProcessDataEntity entity = new ProcessDataEntity();
        entity.setData("INVALID_JSON".getBytes(StandardCharsets.UTF_8));

        when(repository.findById(any())).thenReturn(Optional.of(entity));

        BlockingQueue<Map<String, Object>> queue = new LinkedBlockingQueue<>();
        AtomicBoolean failed = new AtomicBoolean(false);

        service.produceClientData(processId, queue, 1, failed);

        Map<String, Object> first = queue.poll(1, TimeUnit.SECONDS);
        assertThat(first).isEqualTo(ClientDataServiceImpl.END_OF_QUEUE);
    }

    @Test
    void produceClientData_shouldHandleRepositoryException() throws Exception {
        String processId = "proc4";

        when(repository.findClientIdsByProcessId(eq(processId), any()))
                .thenThrow(new RuntimeException("DB down"));

        BlockingQueue<Map<String, Object>> queue = new LinkedBlockingQueue<>();
        AtomicBoolean failed = new AtomicBoolean(false);

        service.produceClientData(processId, queue, 1, failed);

        assertThat(failed).isTrue();

        Map<String, Object> signal = queue.poll(1, TimeUnit.SECONDS);
        assertThat(signal).isEqualTo(ClientDataServiceImpl.END_OF_QUEUE);
    }
}