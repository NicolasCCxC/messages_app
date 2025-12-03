package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Service;

@Service
public class IndexFileAsyncProcessor {

    private final IndexFileProcessorLogic logic;

    public IndexFileAsyncProcessor(IndexFileProcessorLogic logic) {
        this.logic = logic;
    }

    @Async
    public void processIndexFileAsync(String indexFileId, String productId, String period, PathExtractsArchiveIndexDto path, int maxRecordsPerFile, String token) {
        logic.process(indexFileId, productId, period, path, maxRecordsPerFile, token);
    }
}
