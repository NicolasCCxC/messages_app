package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.persistence.entity.id.ProcessDataId;
import avvillas.core.persistence.repository.ProcessDataRepository;
import avvillas.core.service.extract_generation.ClientDataService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.stereotype.Service;

import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.Executor;
import java.util.concurrent.atomic.AtomicBoolean;
import java.util.stream.Stream;

@Service
public class ClientDataServiceImpl implements ClientDataService {

    protected static final Map<String, Object> END_OF_QUEUE = new HashMap<>();
    private static final Logger logger = LoggerFactory.getLogger(ClientDataServiceImpl.class);
    private final ProcessDataRepository processDataRepository;
    private final ObjectMapper objectMapper;
    private final Executor producerTaskExecutor;
    private final ExtractProperties extractProperties;

    public ClientDataServiceImpl(ProcessDataRepository processDataRepository,
                                 ObjectMapper objectMapper,
                                 @Qualifier("producerTaskExecutor") Executor producerTaskExecutor,
                                 ExtractProperties extractProperties) {
        this.processDataRepository = processDataRepository;
        this.objectMapper = objectMapper;
        this.producerTaskExecutor = producerTaskExecutor;
        this.extractProperties = extractProperties;
    }

    @Override
    public void produceClientData(String processId, BlockingQueue<Map<String, Object>> queue, int consumerCount, AtomicBoolean isProcessFailed) {
        producerTaskExecutor.execute(() -> {
            Thread.currentThread().setName("ExtractProducer-" + processId);
            logger.info("[{}] Iniciando producción de datos (Estrategia Paginada)...", processId);

            int pageNumber = 0;
            final int batchSize = extractProperties.getPerformance().getProducerPool().getIdBatchSize();
            Page<String> clientIdsPage;

            try {
                do {

                    if (isProcessFailed.get()) {
                        logger.warn("[{}] Proceso de produccion detenido debido a un fallo en otro hilo.", processId);
                        break;
                    }

                    PageRequest pageable = PageRequest.of(pageNumber, batchSize);
                    clientIdsPage = processDataRepository.findClientIdsByProcessId(processId, pageable);
                    clientIdsPage.getContent().parallelStream().forEach(clientId ->
                        processDataRepository.findById(new ProcessDataId(processId, clientId))
                                .ifPresent(customer -> {
                                    try {
                                        queue.put(deserializeData(customer.getData()));
                                    } catch (Exception e) {
                                        logger.error("[{}] Error encolando cliente {}.", processId, clientId, e);
                                    }
                                })
                    );
                    logger.debug("[{}] Lote de página {} procesado ({} clientes).", processId, pageNumber, clientIdsPage.getNumberOfElements());
                    pageNumber++;
                } while (clientIdsPage.hasNext());

                logger.info("[{}] Producción de datos finalizada. Enviando señales de fin.", processId);
                for (int i = 0; i < consumerCount; i++) {
                    queue.put(END_OF_QUEUE);
                }
            } catch (Exception e) {
                logger.error("[{}] La producción de datos falló catastróficamente.", processId, e);
                isProcessFailed.set(true);
            } finally {
                logger.info("[{}] Produccion finalizada (o fallida). Enviando senales de fin de cola.", processId);
                try {
                    for (int i = 0; i < consumerCount; i++) {
                        queue.put(END_OF_QUEUE);
                    }
                } catch (InterruptedException ex) {
                    Thread.currentThread().interrupt();
                }
            }
        });
    }

    @Override
    public Stream<Map<String, String>> streamClientData(String processId) {
        return Stream.empty();
    }

    private Map<String, Object> deserializeData(byte[] data) throws Exception {
        String jsonString = new String(data, StandardCharsets.UTF_8);
        Map<String, Object> rawData = objectMapper.readValue(jsonString, HashMap.class);
        Map<String, Object> clientData = new HashMap<>();

        for (Map.Entry<String, Object> entry : rawData.entrySet()) {
            String key = entry.getKey();
            Object value = entry.getValue();

            if (value == null) {
                clientData.put(key, "");
            } else if (value instanceof ArrayList) {
                clientData.put(key, value);
            } else {
                clientData.put(key, value.toString());
            }
        }
        return clientData;
    }
}