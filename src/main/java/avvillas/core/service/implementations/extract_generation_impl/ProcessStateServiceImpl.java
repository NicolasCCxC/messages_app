package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.repository.ExtractRepository;
import avvillas.core.service.extract_generation.ProcessStateService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

import java.time.Duration;
import java.time.Instant;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.atomic.AtomicInteger;

@Service
@RequiredArgsConstructor
public class ProcessStateServiceImpl implements ProcessStateService {

    private static final Logger logger = LoggerFactory.getLogger(ProcessStateServiceImpl.class);

    private final ExtractRepository extractRepository;
    private final ExtractProperties extractProperties;

    private final ConcurrentHashMap<String, ProgressState> progressCache = new ConcurrentHashMap<>();

    @Override
    public void registerProcess(String extractId) {
        progressCache.put(extractId, new ProgressState());
        logger.debug("[{}] Proceso registrado en el servicio de estado.", extractId);
    }

    @Override
    @Transactional(propagation = Propagation.REQUIRED)
    public void updateProgress(String extractId, long processedItems, long totalItems) {
        ProgressState state = progressCache.get(extractId);
        if (state == null || totalItems == 0) {
            return;
        }

        int currentPercent = (int) ((double) processedItems / totalItems * 100);
        int lastPercent = state.lastPersistedPercent.get();
        long currentTime = Instant.now().toEpochMilli();
        long timeSinceLastUpdate = currentTime - state.lastPersistedTime;

        boolean shouldUpdate = currentPercent > lastPercent &&
                (currentPercent - lastPercent >= extractProperties.getState().getProgressThresholdPercent() ||
                        timeSinceLastUpdate > extractProperties.getState().getTimeThresholdMs());

        if (shouldUpdate) {
            synchronized (state) {
                if (state.lastPersistedPercent.get() < currentPercent) {
                    state.lastPersistedPercent.set(currentPercent);
                    state.lastPersistedTime = currentTime;
                    persistProgress(extractId, currentPercent, LoadStatus.ACTIVO, null);
                }
            }
        }
    }

    @Override
    @Transactional(propagation = Propagation.REQUIRED)
    public void markAsCompleted(String extractId, String finalMessage) {
        logger.info("[{}] Marcando proceso como FINALIZADO.", extractId);
        persistProgress(extractId, 100, LoadStatus.FINALIZADO, finalMessage);
        progressCache.remove(extractId);
    }

    @Override
    @Transactional(propagation = Propagation.REQUIRED)
    public void markAsFailed(String extractId, String errorMessage) {
        logger.error("[{}] Marcando proceso como ERROR.", extractId);
        persistProgress(extractId, -1, LoadStatus.ERROR, errorMessage);
        progressCache.remove(extractId);
    }

    protected void persistProgress(String extractId, int percent, LoadStatus status, String details) {
        Instant startPersist = Instant.now();
        try {
            String truncatedDetails = details != null ?
                    details.substring(0, Math.min(details.length(), 4000)) : null;

            int updated = extractRepository.updateProgressDirectly(
                    extractId, percent, status, truncatedDetails
            );

            if (updated == 0) {
                logger.error("[{}] No se encontró el extract para actualizar", extractId);
            }

            long persistDuration = Duration.between(startPersist, Instant.now()).toMillis();
            logger.debug("[{}] Progreso persistido en BD en {}ms: {}%, Estado: {}",
                    extractId, persistDuration, percent, status);
        } catch (Exception e) {
            logger.error("[{}] Fallo crítico al persistir el estado en la BD.", extractId, e);
        }
    }

    private static class ProgressState {
        final AtomicInteger lastPersistedPercent = new AtomicInteger(0);
        volatile long lastPersistedTime = 0;
    }
}