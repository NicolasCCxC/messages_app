package avvillas.core.service.specification;

import avvillas.core.constant.fields.IndexFileFieldNames;
import avvillas.core.persistence.entity.IndexFileEntity;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.util.CollectionUtils;

import java.util.List;

public class IndexFileSpecification {

    private IndexFileSpecification() {
    }

    public static Specification<IndexFileEntity> filterByProductIds(List<String> productIds) {
        if (CollectionUtils.isEmpty(productIds)) {
            return (root, query, builder) -> builder.disjunction();
        }
        return (root, query, builder) -> root.get("productId").in(productIds);
    }

    public static Specification<IndexFileEntity> filterByUserIds(List<String> userIds) {
        if (CollectionUtils.isEmpty(userIds)) {
            return (root, query, builder) -> builder.disjunction();
        }
        return (root, query, builder) -> root.get("user").in(userIds);
    }

    public static Specification<IndexFileEntity> filterByDate(String search) {
        final String field = IndexFileFieldNames.CREATED_AT.getFieldName();

        Specification<IndexFileEntity> rangeSpec = SpecificationUtils.filterByDateRange(field, search);

        Specification<IndexFileEntity> likeSpec = SpecificationUtils.timestampLikeIgnoreCase(field, search);
        
        return Specification.where(rangeSpec).or(likeSpec);
    }

    public static Specification<IndexFileEntity> filterByPeriod(String search) {

        return SpecificationUtils.likeIgnoreCase(IndexFileFieldNames.PERIOD.getFieldName(), search);
    }

    public static Specification<IndexFileEntity> filterByStatus(String search) {
        return SpecificationUtils.likeIgnoreCase(IndexFileFieldNames.STATUS.getFieldName(), search);
    }

    public static Specification<IndexFileEntity> filterByPercentAdvance(String search) {
        return SpecificationUtils.equalNumber(IndexFileFieldNames.PERCENT_ADVANCE.getFieldName(), search);
    }
}