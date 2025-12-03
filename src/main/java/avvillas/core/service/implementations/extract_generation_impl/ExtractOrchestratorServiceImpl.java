package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.constant.message.ExtractMessage;
import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.persistence.entity.ExtractEntity;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.mapper.ExtractMapper;
import avvillas.core.persistence.repository.ExtractRepository;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.extract.ExtractDto;
import avvillas.core.service.dto.extract.ExtractResDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.extract_generation.ExtractOrchestratorService;
import avvillas.core.service.extract_generation.ExtractProcessorService;
import avvillas.core.service.extract_generation.ProcessStateService;
import avvillas.core.service.specification.ExtractSpecification;
import avvillas.core.web.traits.LoaderTrait;
import avvillas.core.web.traits.ProductTrait;
import avvillas.core.web.traits.UserTrait;
import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Map;
import java.util.NoSuchElementException;
import java.util.Objects;
import java.util.function.Function;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class ExtractOrchestratorServiceImpl implements ExtractOrchestratorService {

    private static final Logger logger = LoggerFactory.getLogger(ExtractOrchestratorServiceImpl.class);


    private final ExtractProcessorService extractProcessorService;
    private final ProcessStateService processStateService;

    private final LoaderTrait loaderTrait;
    private final ProductTrait productTrait;
    private final UserTrait userTrait;
    private final ExtractRepository extractRepository;
    private final IndexFileRepository indexFileRepository;
    private final ExtractMapper extractMapper;

    @Override
    @Transactional
    public ExtractDto generateExtracts(ExtractDto extractDto, String userId) {
        String productId = extractDto.getProductId();
        String period = extractDto.getPeriod();

        logger.info("[PASO 1] Solicitud de generación recibida para producto {} y período {}.", productId, period);

        extractRepository.findFirstByProductIdAndPeriodOrderByCreatedAtDesc(productId, period)
                .ifPresent(prev -> {
                    if (Objects.equals(prev.getStatus(), LoadStatus.ACTIVO)) {
                        throw new IllegalStateException(String.format(ExtractMessage.ERROR_EXTRACT_PROCESS, productId));
                    }
                });

        IndexFileEntity indexFile = indexFileRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(productId, period, LoadStatus.FINALIZADO)
                .orElseThrow(() -> new NoSuchElementException(
                        String.format(IndexFileMessage.INDEX_FILE_NOT_FOUND_BY_PRODUCT_ID, productId, period)
                ));

        ExtractEntity extractEntity = new ExtractEntity();
        extractEntity.setProductId(productId);
        extractEntity.setPeriod(period);
        extractEntity.setStatus(LoadStatus.ACTIVO);
        extractEntity.setPercentAdvance(0);
        extractEntity.setUser(userId);

        ExtractEntity savedEntity = extractRepository.save(extractEntity);
        String extractId = savedEntity.getId();

        processStateService.registerProcess(extractId);

        String token = ((JwtAuthenticationToken) SecurityContextHolder.getContext().getAuthentication()).getToken().getTokenValue();
        String productName = loaderTrait.getProductById(productId, token).getDescription();

        extractProcessorService.processExtractsAsync(
                extractId,
                productId,
                indexFile.getProcessId(),
                indexFile.getRoute(),
                indexFile.getClientsProcessed(),
                productName,
                token
        );

        logger.info("[PASO 1][ID: {}] Validaciones completadas. Proceso asíncrono lanzado en segundo plano.", extractId);
        return extractMapper.toDto(savedEntity);
    }

    @Override
    public Page<ExtractResDto> searchGlobal(String search, boolean getAllExtracts, Pageable pageable) {

        Specification<ExtractEntity> spec = buildGlobalSearchSpecification(search, getAllExtracts);

        Page<ExtractEntity> resultPage = extractRepository.findAll(spec, pageable);

        return mapToResponsePage(resultPage);
    }

    private Specification<ExtractEntity> buildGlobalSearchSpecification(String search, boolean getAllExtracts) {
        Specification<ExtractEntity> baseSpec = getAllExtracts ?
                Specification.where(null) :
                Specification.where(ExtractSpecification.filterByStatus("FINALIZADO"));

        if (search == null || search.trim().isEmpty()) {
            return baseSpec;
        }

        List<String> productIds = productTrait.searchProducts(search).stream()
                .map(ProductCodeDescriptionDTO::getId)
                .collect(Collectors.toList());

        List<String> userIds = userTrait.searchUsers(search).stream()
                .map(UserNameAndEmailDto::getId)
                .collect(Collectors.toList());


        Specification<ExtractEntity> searchSpec = Specification
                .where(ExtractSpecification.filterByPeriod(search))
                .or(ExtractSpecification.filterByPercentAdvance(search))
                .or(ExtractSpecification.filterByProductIds(productIds))
                .or(ExtractSpecification.filterByUserIds(userIds));

        return baseSpec.and(searchSpec);
    }

    private Page<ExtractResDto> mapToResponsePage(Page<ExtractEntity> resultPage) {
        if (resultPage.isEmpty()) {
            return Page.empty();
        }

        List<String> productIds = resultPage.getContent().stream()
                .map(ExtractEntity::getProductId)
                .filter(Objects::nonNull).distinct().collect(Collectors.toList());

        List<String> userIds = resultPage.getContent().stream()
                .map(ExtractEntity::getUser)
                .filter(Objects::nonNull).distinct().collect(Collectors.toList());

        Map<String, ProductCodeDescriptionDTO> productMap = productTrait.getProductsByIds(productIds).stream()
                .collect(Collectors.toMap(ProductCodeDescriptionDTO::getId, Function.identity()));

        Map<String, UserNameAndEmailDto> userMap = userTrait.getUsersByIds(userIds).stream()
                .collect(Collectors.toMap(UserNameAndEmailDto::getId, Function.identity()));


        return resultPage.map(entity -> new ExtractResDto(
                entity.getId(),
                entity.getPeriod(),
                entity.getStatus().toString(),
                entity.getPercentAdvance(),
                entity.getCreatedAt(),
                entity.getDetails(),
                productMap.get(entity.getProductId()),
                userMap.get(entity.getUser())
        ));
    }

}