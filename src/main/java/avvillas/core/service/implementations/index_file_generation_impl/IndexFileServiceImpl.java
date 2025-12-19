package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.constant.MessageConstant;
import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.mapper.IndexFileMapper;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.service.IndexFileService;
import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.index_file.IndexDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.specification.IndexFileSpecification;
import avvillas.core.web.controller.exception.GlobalExceptionHandler;
import avvillas.core.web.traits.LoaderTrait;
import avvillas.core.web.traits.ProductTrait;
import avvillas.core.web.traits.UserTrait;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;
import org.springframework.stereotype.Service;

import java.util.Base64;
import java.util.List;
import java.util.Objects;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class IndexFileServiceImpl implements IndexFileService {

    private final IndexFileMapper indexFileMapper;
    private final IndexFileRepository indexFileRepository;
    private final IndexFileAsyncProcessor indexFileAsyncProcessor;


    private final LoaderTrait loaderTrait;

    private final ProductTrait productTrait;
    private final UserTrait userTrait;

    @Value("${MAX_RECORDS_PER_FILE}")
    private int maxRecordsPerFile;

    @Override
    public IndexDto generateIndexFile(IndexDto indexDto) {
        String productId = indexDto.getProductId();
        String period = indexDto.getPeriod();
        indexFileRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(productId, period, LoadStatus.ACTIVO)
                .ifPresent(prev -> {
                    if (Objects.equals(prev.getStatus(), LoadStatus.ACTIVO))
                        throw new GlobalExceptionHandler.GlobalMessageException((MessageConstant.format(IndexFileMessage.ERROR_INDEX_FILE_PROCESS, productId)));
                });

        PathExtractsArchiveIndexDto path = loaderTrait.getIndexFilePathByProductId(productId);


        JwtAuthenticationToken authentication = (JwtAuthenticationToken) SecurityContextHolder.getContext().getAuthentication();
        String token = authentication.getToken().getTokenValue();

        loaderTrait.getProductById(productId, token);

        IndexFileEntity indexEntity = indexFileMapper.toEntity(indexDto);
        indexEntity.setRoute(path.getRouteOutputExtract());
        indexEntity.setStatus(LoadStatus.ACTIVO);
        indexEntity.setUser(extractEmailFromToken(token));
        indexEntity.setClientsProcessed(0);
        indexEntity.setPercentAdvance(0);

        IndexFileEntity savedEntity = indexFileRepository.save(indexEntity);

        indexFileAsyncProcessor.processIndexFileAsync(savedEntity.getId(), productId, period, path, maxRecordsPerFile, token);

        return indexFileMapper.toDto(savedEntity);
    }

    @Override
    public Page<IndexDto> searchGlobal(String search, Pageable pageable) {

        if (search == null || search.trim().isEmpty()) {
            return getAll(pageable);
        }

        String trimmedSearch = search.trim();
        
        List<String> productIds = productTrait.searchProducts(trimmedSearch).stream()
                .map(ProductCodeDescriptionDTO::getId)
                .collect(Collectors.toList());

        List<String> userIds = userTrait.searchUsers(trimmedSearch).stream()
                .map(UserNameAndEmailDto::getId)
                .collect(Collectors.toList());


        Specification<IndexFileEntity> globalSpec = (root, query, builder) -> builder.disjunction();

        globalSpec = globalSpec
                .or(IndexFileSpecification.filterByPeriod(trimmedSearch))
                .or(IndexFileSpecification.filterByPercentAdvance(trimmedSearch))
                .or(IndexFileSpecification.filterByDate(trimmedSearch))
                .or(IndexFileSpecification.filterByUserIds(userIds))
                .or(IndexFileSpecification.filterByProductIds(productIds))
                .or(IndexFileSpecification.filterByStatus(trimmedSearch));


        return indexFileRepository.findAll(globalSpec, pageable)
                .map(indexFileMapper::toDto);
    }


    private Page<IndexDto> getAll(Pageable pageable) {
        return indexFileRepository.findAll(pageable)
                .map(indexFileMapper::toDto);
    }

    private String extractEmailFromToken(String token) {
        String[] chunks = token.split("\\.");
        Base64.Decoder decoder = Base64.getUrlDecoder();
        String payload = new String(decoder.decode(chunks[1]));
        return extractValue(payload);
    }

    private String extractValue(String json) {
        try {
            String searchKey = "\"" + "email" + "\":\"";
            int startIndex = json.indexOf(searchKey) + searchKey.length();
            int endIndex = json.indexOf("\"", startIndex);
            return json.substring(startIndex, endIndex);
        } catch (Exception e) {
            return "";
        }
    }
}