package avvillas.core.service;

import avvillas.core.service.dto.extract.ExtractDto;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.scheduling.annotation.Async;

public interface ExtractService {

    ExtractDto generateExtract(ExtractDto extractDto);

    @Async("extractProcessingExecutor")
    void processExtractAsync(String extractId, String productId, String processId, String routeExitExtract, int clientsProcessed, String productName, String token);

    Page<ExtractDto> searchGlobal(String search, boolean getAllExtracts, Pageable pageable);
}