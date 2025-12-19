package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.common.TestDataFactory;
import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.ExtractEntity;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.mapper.ExtractMapper;
import avvillas.core.persistence.repository.ExtractRepository;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.extract.ExtractDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.service.extract_generation.ExtractProcessorService;
import avvillas.core.service.extract_generation.ProcessStateService;
import avvillas.core.service.specification.ExtractSpecification;
import avvillas.core.web.traits.LoaderTrait;
import avvillas.core.web.traits.ProductTrait;
import avvillas.core.web.traits.UserTrait;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.MockedStatic;
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageImpl;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.security.core.context.SecurityContext;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.jwt.Jwt;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;

import java.util.Collections;
import java.util.List;
import java.util.NoSuchElementException;
import java.util.Optional;

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyInt;
import static org.mockito.ArgumentMatchers.anyList;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.mockStatic;
import static org.mockito.Mockito.never;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@MockitoSettings(strictness = Strictness.LENIENT)
class ExtractOrchestratorServiceImplTest extends BaseServiceTest {

    private static final String FAKE_TOKEN = "test-token-12345";
    private static final String FAKE_USER_ID = "user-uuid-111";

    @Mock
    private ExtractProcessorService extractProcessorService;
    @Mock
    private ProcessStateService processStateService;
    @Mock
    private LoaderTrait loaderTrait;
    @Mock
    private ProductTrait productTrait;
    @Mock
    private UserTrait userTrait;
    @Mock
    private ExtractRepository extractRepository;
    @Mock
    private IndexFileRepository indexFileRepository;
    @Mock
    private ExtractMapper extractMapper;

    @InjectMocks
    private ExtractOrchestratorServiceImpl extractOrchestratorService;

    private MockedStatic<SecurityContextHolder> mockSecurityContext;
    private MockedStatic<ExtractSpecification> mockExtractSpecification;

    @Mock
    private SecurityContext mockContext;
    @Mock
    private JwtAuthenticationToken mockAuthToken;
    @Mock
    private Jwt mockJwt;

    @BeforeEach
    void setUp() {
        mockSecurityContext = mockStatic(SecurityContextHolder.class);
        when(SecurityContextHolder.getContext()).thenReturn(mockContext);
        when(mockContext.getAuthentication()).thenReturn(mockAuthToken);
        when(mockAuthToken.getToken()).thenReturn(mockJwt);
        when(mockJwt.getTokenValue()).thenReturn(FAKE_TOKEN);

        mockExtractSpecification = mockStatic(ExtractSpecification.class);
        when(ExtractSpecification.filterByStatus(anyString())).thenReturn(Specification.where(null));
        when(ExtractSpecification.filterByPeriod(anyString())).thenReturn(Specification.where(null));
        when(ExtractSpecification.filterByPercentAdvance(anyString())).thenReturn(Specification.where(null));
        when(ExtractSpecification.filterByDate(anyString())).thenReturn(Specification.where(null));
        when(ExtractSpecification.filterByProductIds(anyList())).thenReturn(Specification.where(null));
        when(ExtractSpecification.filterByUserIds(anyList())).thenReturn(Specification.where(null));
    }

    @AfterEach
    void tearDown() {
        mockSecurityContext.close();
        mockExtractSpecification.close();
    }

    @Test
    @DisplayName("Verifica la generación exitosa cuando no hay proceso activo")
    void shouldGenerateExtractsSuccessfullyWhenNoActiveProcessExists() {
        ExtractDto requestDto = TestDataFactory.mockExtractDtoRequest();
        IndexFileEntity mockIndexFile = TestDataFactory.mockIndexFileEntity();
        ExtractEntity mockSavedEntity = TestDataFactory.mockExtractEntity(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.ACTIVO, FAKE_USER_ID);
        ProductDTO mockProduct = TestDataFactory.mockProductDTO();

        when(extractRepository.findFirstByProductIdAndPeriodOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod()))
                .thenReturn(Optional.empty());
        when(indexFileRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.FINALIZADO))
                .thenReturn(Optional.of(mockIndexFile));
        when(extractRepository.save(any(ExtractEntity.class))).thenReturn(mockSavedEntity);
        when(loaderTrait.getProductById(requestDto.getProductId(), FAKE_TOKEN)).thenReturn(mockProduct);

        extractOrchestratorService.generateExtracts(requestDto, FAKE_USER_ID);

        ArgumentCaptor<ExtractEntity> entityCaptor = ArgumentCaptor.forClass(ExtractEntity.class);
        verify(extractRepository).save(entityCaptor.capture());

        verify(processStateService).registerProcess(mockSavedEntity.getId());
        verify(extractProcessorService).processExtractsAsync(
                eq(mockSavedEntity.getId()),
                eq(requestDto.getProductId()),
                eq(mockIndexFile.getProcessId()),
                eq(mockIndexFile.getRoute()),
                eq(mockIndexFile.getClientsProcessed()),
                eq(mockProduct.getDescription()),
                eq(FAKE_TOKEN)
        );
        verify(extractMapper).toDto(mockSavedEntity);
    }

    @Test
    @DisplayName("Verifica la falla si ya existe un proceso activo")
    void shouldThrowIllegalStateExceptionWhenActiveProcessExists() {
        ExtractDto requestDto = TestDataFactory.mockExtractDtoRequest();
        ExtractEntity activeEntity = TestDataFactory.mockExtractEntity(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.ACTIVO, FAKE_USER_ID);

        when(extractRepository.findFirstByProductIdAndPeriodOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod()))
                .thenReturn(Optional.of(activeEntity));

        Assertions.assertThrows(IllegalStateException.class, () -> {
            extractOrchestratorService.generateExtracts(requestDto, FAKE_USER_ID);
        });

        verify(indexFileRepository, never()).findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(anyString(), anyString(), any(LoadStatus.class));
        verify(extractRepository, never()).save(any(ExtractEntity.class));
        verify(processStateService, never()).registerProcess(anyString());
        verify(extractProcessorService, never()).processExtractsAsync(anyString(), anyString(), anyString(), anyString(), anyInt(), anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la generación exitosa si el proceso anterior está finalizado")
    void shouldGenerateExtractsSuccessfullyWhenPreviousProcessIsFinalizado() {
        ExtractDto requestDto = TestDataFactory.mockExtractDtoRequest();
        ExtractEntity finishedEntity = TestDataFactory.mockExtractEntity(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.FINALIZADO, "other-user");
        IndexFileEntity mockIndexFile = TestDataFactory.mockIndexFileEntity();
        ExtractEntity mockSavedEntity = TestDataFactory.mockExtractEntity(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.ACTIVO, FAKE_USER_ID);
        ProductDTO mockProduct = TestDataFactory.mockProductDTO();

        when(extractRepository.findFirstByProductIdAndPeriodOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod()))
                .thenReturn(Optional.of(finishedEntity));
        when(indexFileRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.FINALIZADO))
                .thenReturn(Optional.of(mockIndexFile));
        when(extractRepository.save(any(ExtractEntity.class))).thenReturn(mockSavedEntity);
        when(loaderTrait.getProductById(requestDto.getProductId(), FAKE_TOKEN)).thenReturn(mockProduct);

        extractOrchestratorService.generateExtracts(requestDto, FAKE_USER_ID);

        verify(extractRepository).save(any(ExtractEntity.class));
        verify(processStateService).registerProcess(mockSavedEntity.getId());
        verify(extractProcessorService).processExtractsAsync(anyString(), anyString(), anyString(), anyString(), anyInt(), anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla si no se encuentra un archivo índice finalizado")
    void shouldThrowNoSuchElementExceptionWhenIndexFileNotFound() {
        ExtractDto requestDto = TestDataFactory.mockExtractDtoRequest();

        when(extractRepository.findFirstByProductIdAndPeriodOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod()))
                .thenReturn(Optional.empty());
        when(indexFileRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(requestDto.getProductId(), requestDto.getPeriod(), LoadStatus.FINALIZADO))
                .thenReturn(Optional.empty());

        Assertions.assertThrows(NoSuchElementException.class, () -> {
            extractOrchestratorService.generateExtracts(requestDto, FAKE_USER_ID);
        });

        verify(extractRepository, never()).save(any(ExtractEntity.class));
        verify(processStateService, never()).registerProcess(anyString());
        verify(extractProcessorService, never()).processExtractsAsync(anyString(), anyString(), anyString(), anyString(), anyInt(), anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la búsqueda global con término y getAllExtracts=true")
    void shouldSearchWithTermAndGetAllExtracts() {
        String search = "202501";
        Pageable pageable = PageRequest.of(0, 10);
        ExtractEntity mockEntity = TestDataFactory.mockExtractEntity("prod-abc", search, LoadStatus.ACTIVO, FAKE_USER_ID);
        Page<ExtractEntity> mockPage = new PageImpl<>(List.of(mockEntity), pageable, 1);
        ProductCodeDescriptionDTO mockProduct = TestDataFactory.mockProductDto();
        UserNameAndEmailDto mockUser = TestDataFactory.mockUserNameAndEmailDto();

        when(productTrait.searchProducts(search)).thenReturn(List.of(mockProduct));
        when(userTrait.searchUsers(search)).thenReturn(List.of(mockUser));
        when(extractRepository.findAll(any(Specification.class), eq(pageable))).thenReturn(mockPage);
        when(productTrait.getProductsByIds(anyList())).thenReturn(List.of(mockProduct));
        when(userTrait.getUsersByIds(anyList())).thenReturn(List.of(mockUser));

        extractOrchestratorService.searchGlobal(search, true, pageable);

        mockExtractSpecification.verify(() -> ExtractSpecification.filterByStatus(anyString()), never());
        verify(productTrait).searchProducts(search);
        verify(userTrait).searchUsers(search);
        verify(extractRepository).findAll(any(Specification.class), eq(pageable));
        verify(productTrait).getProductsByIds(List.of(mockEntity.getProductId()));
        verify(userTrait).getUsersByIds(List.of(mockEntity.getUser()));
    }

    @Test
    @DisplayName("Verifica la búsqueda global con término y getAllExtracts=false")
    void shouldSearchWithTermAndFilterByStatus() {
        String search = "202501";
        Pageable pageable = PageRequest.of(0, 10);
        ExtractEntity mockEntity = TestDataFactory.mockExtractEntity("prod-abc", search, LoadStatus.FINALIZADO, FAKE_USER_ID);
        Page<ExtractEntity> mockPage = new PageImpl<>(List.of(mockEntity), pageable, 1);
        ProductCodeDescriptionDTO mockProduct = TestDataFactory.mockProductDto();
        UserNameAndEmailDto mockUser = TestDataFactory.mockUserNameAndEmailDto();

        when(productTrait.searchProducts(search)).thenReturn(List.of(mockProduct));
        when(userTrait.searchUsers(search)).thenReturn(List.of(mockUser));
        when(extractRepository.findAll(any(Specification.class), eq(pageable))).thenReturn(mockPage);
        when(productTrait.getProductsByIds(anyList())).thenReturn(List.of(mockProduct));
        when(userTrait.getUsersByIds(anyList())).thenReturn(List.of(mockUser));

        extractOrchestratorService.searchGlobal(search, false, pageable);

        mockExtractSpecification.verify(() -> ExtractSpecification.filterByStatus("FINALIZADO"));
        verify(productTrait).searchProducts(search);
        verify(userTrait).searchUsers(search);
        verify(extractRepository).findAll(any(Specification.class), eq(pageable));
    }

    @Test
    @DisplayName("Verifica la búsqueda global con término nulo")
    void shouldSearchWithNullTerm() {
        String search = null;
        Pageable pageable = PageRequest.of(0, 10);

        when(extractRepository.findAll(any(Specification.class), eq(pageable))).thenReturn(Page.empty());

        extractOrchestratorService.searchGlobal(search, false, pageable);

        mockExtractSpecification.verify(() -> ExtractSpecification.filterByStatus("FINALIZADO"));
        verify(productTrait, never()).searchProducts(anyString());
        verify(userTrait, never()).searchUsers(anyString());
        verify(extractRepository).findAll(any(Specification.class), eq(pageable));
    }

    @Test
    @DisplayName("Verifica la búsqueda global con término vacío")
    void shouldSearchWithBlankTerm() {
        String search = " ";
        Pageable pageable = PageRequest.of(0, 10);

        when(extractRepository.findAll(any(Specification.class), eq(pageable))).thenReturn(Page.empty());

        extractOrchestratorService.searchGlobal(search, false, pageable);

        mockExtractSpecification.verify(() -> ExtractSpecification.filterByStatus("FINALIZADO"));
        verify(productTrait, never()).searchProducts(anyString());
        verify(userTrait, never()).searchUsers(anyString());
        verify(extractRepository).findAll(any(Specification.class), eq(pageable));
    }

    @Test
    @DisplayName("Verifica la búsqueda global sin resultados")
    void shouldReturnEmptyPageWhenNoResultsFound() {
        String search = "202501";
        Pageable pageable = PageRequest.of(0, 10);

        when(productTrait.searchProducts(search)).thenReturn(Collections.emptyList());
        when(userTrait.searchUsers(search)).thenReturn(Collections.emptyList());
        when(extractRepository.findAll(any(Specification.class), eq(pageable))).thenReturn(Page.empty());

        extractOrchestratorService.searchGlobal(search, false, pageable);

        verify(extractRepository).findAll(any(Specification.class), eq(pageable));
        verify(productTrait, never()).getProductsByIds(anyList());
        verify(userTrait, never()).getUsersByIds(anyList());
    }
}