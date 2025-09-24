package avvillas.core.web.controller;

import avvillas.core.constant.message.CommonMessage;
import avvillas.core.service.IndexFileService;
import avvillas.core.service.dto.index_file.IndexDto;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

import static avvillas.core.constant.message.IndexFileMessage.INDEX_FILE_PROCESS;

@RestController
@RequestMapping("/index")
@RequiredArgsConstructor
public class IndexFileController {

    private final IndexFileService indexFileService;

    @PostMapping("/file/generate")
    public ResponseEntity<ApiResponse<IndexDto>> generateIndexFile(@Valid @RequestBody IndexDto request) {

        IndexDto indexFile = indexFileService.generateIndexFile(request);

        return ResponseEntity.status(HttpStatus.CREATED)
                .body(ApiResponse.success(indexFile, INDEX_FILE_PROCESS, null));
    }

    @GetMapping
    public ResponseEntity<ApiResponse<Page<IndexDto>>> getAll(
            @RequestParam(required = false) String search,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "10") int size) {
        Page<IndexDto> products = indexFileService.searchGlobal(search, PageRequest.of(page, size));
        return ResponseEntity.status(HttpStatus.OK)
                .body(ApiResponse.success(products, CommonMessage.QUERY_SUCCESS, null));
    }

}