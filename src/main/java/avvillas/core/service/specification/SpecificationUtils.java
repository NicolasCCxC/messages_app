package avvillas.core.service.specification;

import jakarta.persistence.criteria.Expression;
import jakarta.persistence.criteria.Path;
import org.springframework.data.jpa.domain.Specification;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.format.DateTimeParseException;


public class SpecificationUtils {

    private SpecificationUtils() {
    }

    public static <T> Specification<T> likeIgnoreCase(String fieldName, String search) {
        if (search == null || search.trim().isEmpty()) {
            return (root, query, builder) -> builder.disjunction();
        }

        String searchPattern = "%" + search.trim().toLowerCase() + "%";

        return (root, query, builder) -> {
            Path<String> path = root.get(fieldName);
            return builder.like(builder.lower(path), searchPattern);
        };
    }

    public static <T> Specification<T> equalNumber(String fieldName, String search) {
        if (search == null || search.trim().isEmpty()) {
            return (root, query, builder) -> builder.disjunction();
        }

        try {
            Integer numericValue = Integer.parseInt(search.trim());

            return (root, query, builder) -> {
                Path<Integer> path = root.get(fieldName);
                return builder.equal(path, numericValue);
            };
        } catch (NumberFormatException e) {
            return (root, query, builder) -> builder.disjunction();
        }
    }

    public static <T> Specification<T> filterByDateRange(String fieldName, String search) {
        if (search == null || search.trim().isEmpty()) {
            return (root, query, builder) -> builder.disjunction();
        }

        LocalDate searchDate;
        try {
            searchDate = LocalDate.parse(search.trim());
        } catch (DateTimeParseException e) {
            return (root, query, builder) -> builder.disjunction();
        }

        LocalDateTime startOfDay = searchDate.atStartOfDay();
        LocalDateTime endOfDay = searchDate.plusDays(1).atStartOfDay();

        return (root, query, builder) -> {
            Path<LocalDateTime> path = root.get(fieldName);
            return builder.between(path, startOfDay, endOfDay);
        };
    }

    public static <T> Specification<T> timestampLikeIgnoreCase(String fieldName, String search) {
        if (search == null || search.trim().isEmpty()) {
            return (root, query, builder) -> builder.disjunction();
        }

        final String searchPattern = "%" + search.trim().replace('T', ' ') + "%";

        return (root, query, builder) -> {

            final String dbFunctionName = "TO_CHAR";

            final String dbFormatPattern = "YYYY-MM-DD HH24:MI:SS.FF6";

            Expression<String> timestampAsKnownString = builder.function(
                    dbFunctionName,
                    String.class,
                    root.get(fieldName),
                    builder.literal(dbFormatPattern)
            );

            return builder.like(timestampAsKnownString, searchPattern);
        };
    }
}