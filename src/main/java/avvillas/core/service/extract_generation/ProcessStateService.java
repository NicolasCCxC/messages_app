package avvillas.core.service.extract_generation;


public interface ProcessStateService {

    void registerProcess(String extractId);

    void updateProgress(String extractId, long processedItems, long totalItems);

    void markAsCompleted(String extractId, String finalMessage);
    
    void markAsFailed(String extractId, String errorMessage);
}