package avvillas.core.service.extract_generation;

import java.util.Map;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.atomic.AtomicBoolean;
import java.util.stream.Stream;

public interface ClientDataService {
    void produceClientData(String processId, BlockingQueue<Map<String, Object>> queue, int consumerCount, AtomicBoolean isProcessFailed);

    Stream<Map<String, String>> streamClientData(String processId);
}