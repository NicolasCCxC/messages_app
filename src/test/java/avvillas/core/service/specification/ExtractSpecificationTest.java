package avvillas.core.service.specification;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.persistence.entity.ExtractEntity;
import jakarta.persistence.criteria.CriteriaBuilder;
import jakarta.persistence.criteria.CriteriaQuery;
import jakarta.persistence.criteria.Path;
import jakarta.persistence.criteria.Predicate;
import jakarta.persistence.criteria.Root;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.Mock;
import org.mockito.MockedStatic;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.util.CollectionUtils;

import java.lang.reflect.Constructor;
import java.util.Collections;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.Mockito.mockStatic;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

class ExtractSpecificationTest extends BaseServiceTest {

    @Mock
    private Root<ExtractEntity> root;
    @Mock
    private CriteriaQuery<?> query;
    @Mock
    private CriteriaBuilder builder;
    @Mock
    private Path<Object> path;
    @Mock
    private Predicate predicate;

    private MockedStatic<SpecificationUtils> specUtilsMock;
    private MockedStatic<CollectionUtils> collUtilsMock;

    @BeforeEach
    void setUp() {
        specUtilsMock = mockStatic(SpecificationUtils.class);
        collUtilsMock = mockStatic(CollectionUtils.class);
    }

    @AfterEach
    void tearDown() {
        specUtilsMock.close();
        collUtilsMock.close();
    }

    @Test
    @DisplayName("Verifica la invocación del constructor privado para cobertura")
    void shouldCoverPrivateConstructor() throws Exception {
        Constructor<ExtractSpecification> constructor = ExtractSpecification.class.getDeclaredConstructor();
        constructor.setAccessible(true);
        ExtractSpecification instance = constructor.newInstance();
        assertThat(instance).isNotNull();
    }

    @Test
    @DisplayName("Verifica que filterByPeriod llame a SpecificationUtils")
    void filterByPeriod_shouldCallSpecificationUtils() {
        String search = "202501";
        ExtractSpecification.filterByPeriod(search);
        // CORRECCIÓN: Cambiado de "periodo" a "period"
        specUtilsMock.verify(() -> SpecificationUtils.likeIgnoreCase("period", search));
    }

    @Test
    @DisplayName("Verifica que filterByStatus llame a SpecificationUtils")
    void filterByStatus_shouldCallSpecificationUtils() {
        String search = "ACTIVO";
        ExtractSpecification.filterByStatus(search);
        // CORRECCIÓN: Cambiado de "estado" a "status"
        specUtilsMock.verify(() -> SpecificationUtils.likeIgnoreCase("status", search));
    }

    @Test
    @DisplayName("Verifica que filterByPercentAdvance llame a SpecificationUtils")
    void filterByPercentAdvance_shouldCallSpecificationUtils() {
        String search = "100";
        ExtractSpecification.filterByPercentAdvance(search);
        // CORRECCIÓN: Cambiado de "avance" a "percentAdvance"
        specUtilsMock.verify(() -> SpecificationUtils.equalNumber("percentAdvance", search));
    }

    @Test
    @DisplayName("Verifica que filterByCreatedAt llame a SpecificationUtils")
    void filterByCreatedAt_shouldCallSpecificationUtils() {
        String search = "2025-01-01";
        ExtractSpecification.filterByDate(search);
        specUtilsMock.verify(() -> SpecificationUtils.timestampLikeIgnoreCase("createdAt", search));
    }

    @Test
    @DisplayName("Verifica que filterByProductIds retorne nulo si la lista es vacía")
    void filterByProductIds_shouldReturnNullWhenListIsEmpty() {
        List<String> emptyList = Collections.emptyList();
        collUtilsMock.when(() -> CollectionUtils.isEmpty(emptyList)).thenReturn(true);

        Specification<ExtractEntity> spec = ExtractSpecification.filterByProductIds(emptyList);

        assertThat(spec).isNull();
    }

    @Test
    @DisplayName("Verifica que filterByProductIds cree una especificación 'IN' válida")
    void filterByProductIds_shouldCreateInSpecification() {
        List<String> productIds = List.of("p1", "p2");
        collUtilsMock.when(() -> CollectionUtils.isEmpty(productIds)).thenReturn(false);

        when(root.get("productId")).thenReturn(path);
        when(path.in(productIds)).thenReturn(predicate);

        Specification<ExtractEntity> spec = ExtractSpecification.filterByProductIds(productIds);
        Predicate result = spec.toPredicate(root, query, builder);

        assertThat(result).isEqualTo(predicate);
        verify(root).get("productId");
    }

    @Test
    @DisplayName("Verifica que filterByUserIds retorne nulo si la lista es vacía")
    void filterByUserIds_shouldReturnNullWhenListIsEmpty() {
        List<String> emptyList = Collections.emptyList();
        collUtilsMock.when(() -> CollectionUtils.isEmpty(emptyList)).thenReturn(true);

        Specification<ExtractEntity> spec = ExtractSpecification.filterByUserIds(emptyList);

        assertThat(spec).isNull();
    }

    @Test
    @DisplayName("Verifica que filterByUserIds cree una especificación 'IN' válida")
    void filterByUserIds_shouldCreateInSpecification() {
        List<String> userIds = List.of("u1", "u2");
        collUtilsMock.when(() -> CollectionUtils.isEmpty(userIds)).thenReturn(false);

        when(root.get("user")).thenReturn(path);
        when(path.in(userIds)).thenReturn(predicate);

        Specification<ExtractEntity> spec = ExtractSpecification.filterByUserIds(userIds);
        Predicate result = spec.toPredicate(root, query, builder);

        assertThat(result).isEqualTo(predicate);
        verify(root).get("user");
    }
}