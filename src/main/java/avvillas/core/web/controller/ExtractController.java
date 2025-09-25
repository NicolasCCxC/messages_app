package avvillas.core.web.controller;

import avvillas.core.constant.message.CommonMessage;
import avvillas.core.service.dto.extract.ExtractDto;
import avvillas.core.service.dto.extract.ExtractResDto;
import avvillas.core.service.extract_generation.ExtractOrchestratorService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import static avvillas.core.constant.message.ExtractMessage.EXTRACT_PROCESS;

@RestController
@RequestMapping("/extract")
@RequiredArgsConstructor
public class ExtractController {

    private final ExtractOrchestratorService extractService;

    @PostMapping("/generate")
    public ResponseEntity<ApiResponse<ExtractDto>> generateExtract(@Valid @RequestBody ExtractDto request, Authentication authentication) {
        Authentication auth = authentication != null ? authentication : SecurityContextHolder.getContext().getAuthentication();
        String userId = auth != null ? auth.getName() : null;
        ExtractDto extract = extractService.generateExtracts(request, userId);

        return ResponseEntity.status(HttpStatus.CREATED)
                .body(ApiResponse.success(extract, EXTRACT_PROCESS, null));
    }

    @GetMapping
    public ResponseEntity<ApiResponse<Page<ExtractResDto>>> getAll(
            @RequestParam(required = false) String search,
            @RequestParam(required = false, defaultValue = "false") boolean getAllExtracts,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "10") int size
    ) {
        Page<ExtractResDto> products = extractService.searchGlobal(search, getAllExtracts, PageRequest.of(page, size));
        return ResponseEntity.status(HttpStatus.OK)
                .body(ApiResponse.success(products, CommonMessage.QUERY_SUCCESS, null));
    }
}