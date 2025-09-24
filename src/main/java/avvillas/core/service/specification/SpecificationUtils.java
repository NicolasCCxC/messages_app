package avvillas.core.service.specification;

import org.springframework.data.jpa.domain.Specification;

public class SpecificationUtils {

    private SpecificationUtils() {}

    public static <T> Specification<T> likeIgnoreCase(String field, String value) {
        return (root, query, cb) -> {
            if (value == null || value.trim().isEmpty())  return null;

            String searchLower = value.toLowerCase().trim();
            return cb.like(cb.lower(root.get(field)), "%" + searchLower + "%");
        };
    }

    public static <T> Specification<T> equalNumber(String field, String value) {
        return (root, query, cb) -> {
            if (value == null || value.trim().isEmpty())  return null;

            try {
                Integer numericValue = Integer.parseInt(value.trim());
                return cb.equal(root.get(field), numericValue);
            } catch (NumberFormatException e) {
                return null;
            }
        };
    }
}
