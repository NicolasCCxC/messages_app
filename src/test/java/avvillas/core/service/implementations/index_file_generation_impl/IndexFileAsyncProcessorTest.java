package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import org.junit.jupiter.api.Test;
import org.mockito.Mockito;

class IndexFileAsyncProcessorTest {

    @Test
    void shouldDelegateToProcessorLogic() {
        IndexFileProcessorLogic logic = Mockito.mock(IndexFileProcessorLogic.class);
        IndexFileAsyncProcessor asyncProcessor = new IndexFileAsyncProcessor(logic);

        PathExtractsArchiveIndexDto path = new PathExtractsArchiveIndexDto();
        asyncProcessor.processIndexFileAsync("id-1", "prod-1", "2025-09", path, 5, "token-123");

        Mockito.verify(logic).process("id-1", "prod-1", "2025-09", path, 5, "token-123");
    }
}
