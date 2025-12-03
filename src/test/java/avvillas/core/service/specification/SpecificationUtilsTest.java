package avvillas.core.service.specification;

import jakarta.persistence.criteria.*;
import org.junit.jupiter.api.Test;

import static org.assertj.core.api.Assertions.assertThat;

import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;
import static org.mockito.Mockito.verify;

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
    void likeIgnoreCase_shouldReturnNull_whenValueIsNullOrEmpty() {
        var spec1 = SpecificationUtils.likeIgnoreCase("name", null);
        var spec2 = SpecificationUtils.likeIgnoreCase("name", "  ");

        assertThat(spec1.toPredicate(null, null, null)).isNull();
        assertThat(spec2.toPredicate(null, null, null)).isNull();
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
    void equalNumber_shouldReturnNull_whenValueIsInvalid() {
        var spec = SpecificationUtils.equalNumber("age", "abc");
        var result = spec.toPredicate(null, null, null);

        assertThat(result).isNull();
    }

    @Test
    void equalNumber_shouldReturnNull_whenValueIsNullOrEmpty() {
        var spec1 = SpecificationUtils.equalNumber("age", null);
        var spec2 = SpecificationUtils.equalNumber("age", "   ");

        assertThat(spec1.toPredicate(null, null, null)).isNull();
        assertThat(spec2.toPredicate(null, null, null)).isNull();
    }
}
