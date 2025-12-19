package avvillas.core.service.specification;

import jakarta.persistence.criteria.CriteriaBuilder;
import jakarta.persistence.criteria.Expression;
import jakarta.persistence.criteria.Path;
import jakarta.persistence.criteria.Predicate;
import jakarta.persistence.criteria.Root;
import org.junit.jupiter.api.Test;
import org.springframework.data.jpa.domain.Specification;

import java.time.LocalDate;
import java.time.LocalDateTime;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.times;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

class SpecificationUtilsTest {

    @Test
    void likeIgnoreCase_shouldReturnPredicate_whenValueIsValid() {
        Root<Object> root = mock(Root.class);
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Path path = mock(Path.class);
        Predicate predicate = mock(Predicate.class);

        when(root.get("name")).thenReturn(path);
        when(cb.lower(path)).thenReturn(path);
        when(cb.like(path, "%john%")).thenReturn(predicate);

        var spec = SpecificationUtils.likeIgnoreCase("name", " John ");
        var result = spec.toPredicate(root, null, cb);

        assertThat(result).isNotNull();
        verify(cb).like(path, "%john%");
    }

    @Test
    void likeIgnoreCase_shouldReturnDisjunction_whenValueIsNullOrEmpty() {
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Predicate predicate = mock(Predicate.class);
        when(cb.disjunction()).thenReturn(predicate);

        var spec1 = SpecificationUtils.likeIgnoreCase("name", null);
        var spec2 = SpecificationUtils.likeIgnoreCase("name", "  ");

        assertThat(spec1.toPredicate(null, null, cb)).isNotNull();
        assertThat(spec2.toPredicate(null, null, cb)).isNotNull();

        verify(cb, times(2)).disjunction();
    }

    @Test
    void equalNumber_shouldReturnPredicate_whenValueIsNumeric() {
        Root<Object> root = mock(Root.class);
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Path path = mock(Path.class);
        Predicate predicate = mock(Predicate.class);

        when(root.get("age")).thenReturn(path);
        when(cb.equal(path, 25)).thenReturn(predicate);

        var spec = SpecificationUtils.equalNumber("age", "25");
        var result = spec.toPredicate(root, null, cb);

        assertThat(result).isNotNull();
        verify(cb).equal(path, 25);
    }

    @Test
    void equalNumber_shouldReturnDisjunction_whenValueIsInvalid() {
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Predicate predicate = mock(Predicate.class);
        when(cb.disjunction()).thenReturn(predicate);

        var spec = SpecificationUtils.equalNumber("age", "abc");

        assertThat(spec.toPredicate(null, null, cb)).isNotNull();
        verify(cb).disjunction();
    }

    @Test
    void equalNumber_shouldReturnDisjunction_whenValueIsNullOrEmpty() {
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Predicate predicate = mock(Predicate.class);
        when(cb.disjunction()).thenReturn(predicate);

        var spec1 = SpecificationUtils.equalNumber("age", null);
        var spec2 = SpecificationUtils.equalNumber("age", "   ");

        assertThat(spec1.toPredicate(null, null, cb)).isNotNull();
        assertThat(spec2.toPredicate(null, null, cb)).isNotNull();
        verify(cb, times(2)).disjunction();
    }

    @Test
    void timestampLikeIgnoreCase_shouldReturnPredicate_whenValueIsValid() {
        Root<Object> root = mock(Root.class);
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Path<Object> timestampField = mock(Path.class);
        Expression<String> formatLiteral = mock(Expression.class);
        Expression<String> functionExpression = mock(Expression.class);
        Predicate likePredicate = mock(Predicate.class);

        when(root.get("createdAt")).thenReturn(timestampField);
        when(cb.literal("YYYY-MM-DD HH24:MI:SS.FF6")).thenReturn(formatLiteral);
        when(cb.function("TO_CHAR", String.class, timestampField, formatLiteral)).thenReturn(functionExpression);
        when(cb.like(functionExpression, "%2025-10-03 21:17%")).thenReturn(likePredicate);

        var spec = SpecificationUtils.timestampLikeIgnoreCase("createdAt", "2025-10-03T21:17");
        var result = spec.toPredicate(root, null, cb);

        assertThat(result).isNotNull();
        verify(cb).like(functionExpression, "%2025-10-03 21:17%");
    }

    @Test
    void timestampLikeIgnoreCase_shouldReturnDisjunction_whenValueIsNullOrEmpty() {
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Predicate predicate = mock(Predicate.class);
        when(cb.disjunction()).thenReturn(predicate);

        Specification<Object> spec1 = SpecificationUtils.timestampLikeIgnoreCase("createdAt", null);
        Specification<Object> spec2 = SpecificationUtils.timestampLikeIgnoreCase("createdAt", "  ");

        assertThat(spec1.toPredicate(null, null, cb)).isNotNull();
        assertThat(spec2.toPredicate(null, null, cb)).isNotNull();
        verify(cb, times(2)).disjunction();
    }


    @Test
    void filterByDateRange_shouldReturnPredicate_whenValueIsValidDate() {
        Root<Object> root = mock(Root.class);
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Path<LocalDateTime> path = mock(Path.class);
        Predicate predicate = mock(Predicate.class);

        when(root.<LocalDateTime>get("createdAt")).thenReturn(path);

        LocalDate searchDate = LocalDate.parse("2025-10-30");
        LocalDateTime startOfDay = searchDate.atStartOfDay();
        LocalDateTime endOfDay = searchDate.plusDays(1).atStartOfDay();

        when(cb.between(path, startOfDay, endOfDay)).thenReturn(predicate);

        var spec = SpecificationUtils.filterByDateRange("createdAt", "2025-10-30");
        var result = spec.toPredicate(root, null, cb);

        assertThat(result).isNotNull();
        verify(cb).between(path, startOfDay, endOfDay);
    }

    @Test
    void filterByDateRange_shouldReturnDisjunction_whenValueIsInvalidDate() {
        CriteriaBuilder cb = mock(CriteriaBuilder.class);
        Predicate predicate = mock(Predicate.class);
        when(cb.disjunction()).thenReturn(predicate);

        var spec = SpecificationUtils.filterByDateRange("createdAt", "esto-no-es-una-fecha");

        assertThat(spec.toPredicate(null, null, cb)).isNotNull();
        verify(cb).disjunction();
    }
}