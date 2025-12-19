package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.mapper.IndexFileMapper;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.index_file.IndexDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.web.controller.exception.GlobalExceptionHandler;
import avvillas.core.web.traits.LoaderTrait;
import avvillas.core.web.traits.ProductTrait;
import avvillas.core.web.traits.UserTrait;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.mockito.ArgumentCaptor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageImpl;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.jwt.Jwt;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;

import java.util.Base64;
import java.util.Collections;
import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.Mockito.any;
import static org.mockito.Mockito.anyInt;
import static org.mockito.Mockito.anyString;
import static org.mockito.Mockito.eq;
import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;


class IndexFileServiceImplTest {

    private IndexFileServiceImpl service;
    private IndexFileMapper mapper;
    private IndexFileRepository repository;
    private IndexFileAsyncProcessor asyncProcessor;
    private LoaderTrait loaderTrait;
    private ProductTrait productTrait;
    private UserTrait userTrait;

    @BeforeEach
    void setUp() {
        mapper = mock(IndexFileMapper.class);
        repository = mock(IndexFileRepository.class);
        asyncProcessor = mock(IndexFileAsyncProcessor.class);
        loaderTrait = mock(LoaderTrait.class);
        productTrait = mock(ProductTrait.class);
        userTrait = mock(UserTrait.class);

        service = new IndexFileServiceImpl(mapper, repository, asyncProcessor, loaderTrait, productTrait, userTrait);
    }

    @Test
    void givenActiveProcess_whenGenerateIndexFile_thenThrowsException() {
        IndexDto dto = new IndexDto();
        dto.setProductId("prod-1");
        dto.setPeriod("2025-09");

        IndexFileEntity active = new IndexFileEntity();
        active.setStatus(LoadStatus.ACTIVO);
        when(repository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(any(), any(), any()))
                .thenReturn(Optional.of(active));

        assertThatThrownBy(() -> service.generateIndexFile(dto))
                .isInstanceOf(GlobalExceptionHandler.GlobalMessageException.class)
                .hasMessageContaining(String.format(IndexFileMessage.ERROR_INDEX_FILE_PROCESS, "prod-1"));
    }

    @Test
    void givenValidDto_whenGenerateIndexFile_thenSavesAndDelegatesToAsync() {
        // --- Arrange ---
        IndexDto dto = new IndexDto();
        dto.setProductId("prod-1");
        dto.setPeriod("2025-09");

        ProductDTO productDTO = new ProductDTO();
        productDTO.setId("P1");
        productDTO.setCode("CODE1");
        productDTO.setDescription("Producto de prueba");

        PathExtractsArchiveIndexDto path = new PathExtractsArchiveIndexDto();
        path.setRouteOutputExtract("/tmp");

        when(repository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(any(), any(), any()))
                .thenReturn(Optional.empty());
        when(loaderTrait.getIndexFilePathByProductId(any())).thenReturn(path);
        when(loaderTrait.getProductById(any(), any())).thenReturn(productDTO);

        IndexFileEntity entity = new IndexFileEntity();
        entity.setId("id-1");

        when(mapper.toEntity(dto)).thenReturn(entity);
        when(repository.save(any(IndexFileEntity.class))).thenReturn(entity);
        when(mapper.toDto(entity)).thenReturn(dto);

        // --- Mock del Token JWT (ACTUALIZADO) ---
        String expectedEmail = "test@correo.com";
        String payload = Base64.getUrlEncoder().withoutPadding()
                .encodeToString(String.format("{\"email\":\"%s\"}", expectedEmail).getBytes());
        String fakeJwt = "header." + payload + ".signature";

        Jwt jwt = Jwt.withTokenValue(fakeJwt)
                .header("alg", "none")
                .claim("email", expectedEmail)
                .build();

        JwtAuthenticationToken auth = new JwtAuthenticationToken(jwt);
        SecurityContextHolder.getContext().setAuthentication(auth);

        service.generateIndexFile(dto);

        ArgumentCaptor<IndexFileEntity> entityCaptor = ArgumentCaptor.forClass(IndexFileEntity.class);
        verify(repository).save(entityCaptor.capture());

        IndexFileEntity savedEntity = entityCaptor.getValue();
        assertThat(savedEntity.getUser()).isEqualTo(expectedEmail);
        assertThat(savedEntity.getStatus()).isEqualTo(LoadStatus.ACTIVO);
        assertThat(savedEntity.getRoute()).isEqualTo(path.getRouteOutputExtract());

        verify(asyncProcessor).processIndexFileAsync(eq("id-1"), eq("prod-1"), eq("2025-09"), eq(path), anyInt(), anyString());
    }

    @Test
    void shouldSearchGlobalWithEmptySearch() {
        when(repository.findAll(any(Pageable.class))).thenReturn(new PageImpl<>(List.of()));
        Page<?> result = service.searchGlobal(null, Pageable.ofSize(5));
        assert (result.isEmpty());
    }

    @Test
    void shouldSearchGlobalWithStatusFilter() {
        String search = "activo";
        Pageable pageable = Pageable.ofSize(5);

        IndexFileEntity entity = new IndexFileEntity();
        entity.setStatus(LoadStatus.ACTIVO);

        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));
        when(mapper.toDto(any())).thenReturn(new IndexDto());

        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();
        verify(repository).findAll(any(Specification.class), eq(pageable));
    }

    @Test
    void shouldSearchGlobalWithProductIdFilter() {
        String search = "prod-123";
        Pageable pageable = Pageable.ofSize(5);

        IndexFileEntity entity = new IndexFileEntity();
        entity.setProductId("prod-123");

        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));
        when(mapper.toDto(any())).thenReturn(new IndexDto());

        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();
    }

    @Test
    void shouldSearchGlobalWithPeriodFilter() {
        String search = "2025-10";
        Pageable pageable = Pageable.ofSize(5);

        IndexFileEntity entity = new IndexFileEntity();
        entity.setPeriod("2025-10");

        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));
        when(mapper.toDto(any())).thenReturn(new IndexDto());

        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();
    }

    @Test
    void shouldSearchGlobalWithPercentAdvanceFilter() {
        String search = "50";
        Pageable pageable = Pageable.ofSize(5);

        IndexFileEntity entity = new IndexFileEntity();
        entity.setPercentAdvance(50);

        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));
        when(mapper.toDto(any())).thenReturn(new IndexDto());

        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();
    }


    @Test
    void shouldSearchGlobalWithCreatedAtFilter() {
        String search = "2025-10-03T21:17";
        Pageable pageable = Pageable.ofSize(5);

        IndexFileEntity entity = new IndexFileEntity();

        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));
        when(mapper.toDto(any())).thenReturn(new IndexDto());

        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();
    }

    @Test
    void shouldSearchGlobalWithSearchTerm() {

        String search = "2025-10-30";
        Pageable pageable = Pageable.ofSize(5);

        ProductCodeDescriptionDTO productDto = new ProductCodeDescriptionDTO("prod-id-1", "P1", "Ahorros");
        when(productTrait.searchProducts(eq(search))).thenReturn(List.of(productDto));

        UserNameAndEmailDto userDto = new UserNameAndEmailDto("user-id-1", "Test User", "test@user.com");
        when(userTrait.searchUsers(eq(search))).thenReturn(List.of(userDto));


        IndexFileEntity entity = new IndexFileEntity();
        entity.setId("test-id");
        entity.setPeriod("2025-10");
        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));

        when(mapper.toDto(any(IndexFileEntity.class))).thenReturn(new IndexDto());

        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();

        verify(productTrait).searchProducts(eq(search));
        verify(userTrait).searchUsers(eq(search));

        verify(repository).findAll(any(Specification.class), eq(pageable));
    }


    @Test
    void shouldSearchGlobalWithSearchTerm_whenTraitsReturnEmpty() {

        String search = "un-termino-raro";
        Pageable pageable = Pageable.ofSize(5);


        when(productTrait.searchProducts(eq(search))).thenReturn(Collections.emptyList());
        when(userTrait.searchUsers(eq(search))).thenReturn(Collections.emptyList());


        IndexFileEntity entity = new IndexFileEntity();
        entity.setStatus(LoadStatus.ACTIVO);

        when(repository.findAll(any(Specification.class), eq(pageable)))
                .thenReturn(new PageImpl<>(List.of(entity)));
        when(mapper.toDto(any(IndexFileEntity.class))).thenReturn(new IndexDto());


        Page<IndexDto> result = service.searchGlobal(search, pageable);

        assertThat(result).isNotEmpty();
        verify(productTrait).searchProducts(eq(search));
        verify(userTrait).searchUsers(eq(search));
        verify(repository).findAll(any(Specification.class), eq(pageable));
    }

}
