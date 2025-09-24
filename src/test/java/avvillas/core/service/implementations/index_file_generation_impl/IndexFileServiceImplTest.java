package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.mapper.IndexFileMapper;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.service.dto.index_file.IndexDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.service.dto.product.ProductDTO;
import avvillas.core.web.controller.exception.GlobalExceptionHandler;
import avvillas.core.web.traits.LoaderTrait;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageImpl;
import org.springframework.data.domain.Pageable;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.oauth2.jwt.Jwt;
import org.springframework.security.oauth2.server.resource.authentication.JwtAuthenticationToken;

import java.util.Base64;
import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThatThrownBy;

import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;
import static org.mockito.Mockito.any;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.anyInt;
import static org.mockito.Mockito.anyString;
import static org.mockito.Mockito.eq;

class IndexFileServiceImplTest {

    private IndexFileServiceImpl service;
    private IndexFileMapper mapper;
    private IndexFileRepository repository;
    private IndexFileAsyncProcessor asyncProcessor;
    private LoaderTrait loaderTrait;

    @BeforeEach
    void setUp() {
        mapper = mock(IndexFileMapper.class);
        repository = mock(IndexFileRepository.class);
        asyncProcessor = mock(IndexFileAsyncProcessor.class);
        loaderTrait = mock(LoaderTrait.class);

        service = new IndexFileServiceImpl(mapper, repository, asyncProcessor, loaderTrait);
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


        String payload = Base64.getUrlEncoder().withoutPadding()
                .encodeToString("{\"email\":\"test@correo.com\"}".getBytes());

        String fakeJwt = "header." + payload + ".signature";

        Jwt jwt = Jwt.withTokenValue(fakeJwt)
                .header("alg", "none")
                .claim("email", "test@correo.com")
                .build();

        JwtAuthenticationToken auth = new JwtAuthenticationToken(jwt);
        SecurityContextHolder.getContext().setAuthentication(auth);

        service.generateIndexFile(dto);

        verify(repository).save(any(IndexFileEntity.class));
        verify(asyncProcessor).processIndexFileAsync(eq("id-1"), eq("prod-1"), eq("2025-09"), eq(path), anyInt(), anyString());
    }

    @Test
    void shouldSearchGlobalWithEmptySearch() {
        when(repository.findAll(any(Pageable.class))).thenReturn(new PageImpl<>(List.of()));
        Page<?> result = service.searchGlobal(null, Pageable.ofSize(5));
        assert (result.isEmpty());
    }
}
