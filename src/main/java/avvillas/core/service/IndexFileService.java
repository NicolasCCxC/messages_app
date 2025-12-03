package avvillas.core.service;

import avvillas.core.service.dto.index_file.IndexDto;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

public interface IndexFileService {
    
    IndexDto generateIndexFile(IndexDto indexDto);

    Page<IndexDto> searchGlobal(String search, Pageable pageable);
}