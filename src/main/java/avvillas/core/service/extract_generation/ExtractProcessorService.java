package avvillas.core.service.extract_generation;

import org.springframework.scheduling.annotation.Async;

public interface ExtractProcessorService {

    @Async("extractProcessingExecutor")
    void processExtractsAsync(String extractId, String productId, String processId,
                              String routeExitExtract, int clientsProcessed,
                              String productName, String token);
}