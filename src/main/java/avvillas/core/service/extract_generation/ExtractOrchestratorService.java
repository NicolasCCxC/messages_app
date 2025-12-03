package avvillas.core.service.extract_generation;

import avvillas.core.service.dto.extract.ExtractDto;
import avvillas.core.service.dto.extract.ExtractResDto;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

public interface ExtractOrchestratorService {

    ExtractDto generateExtracts(ExtractDto extractDto, String userId);

    Page<ExtractResDto> searchGlobal(String search, boolean getAllExtracts, Pageable pageable);
}