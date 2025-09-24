package avvillas.core.service.specification;

import avvillas.core.constant.fields.IndexFileFieldNames;
import avvillas.core.persistence.entity.ExtractEntity;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.util.CollectionUtils;

import java.util.List;

public class ExtractSpecification {

    private ExtractSpecification() {}

    public static Specification<ExtractEntity> filterByPeriod(String search) {

        return SpecificationUtils.likeIgnoreCase(IndexFileFieldNames.PERIOD.getFieldName(), search);
    }

    public static Specification<ExtractEntity> filterByStatus(String search) {
        return SpecificationUtils.likeIgnoreCase(IndexFileFieldNames.STATUS.getFieldName(), search);
    }

    public static Specification<ExtractEntity> filterByPercentAdvance(String search) {
        return SpecificationUtils.equalNumber(IndexFileFieldNames.PERCENT_ADVANCE.getFieldName(), search);
    }

    public static Specification<ExtractEntity> filterByProductIds(List<String> productIds) {
        if (CollectionUtils.isEmpty(productIds)) {
            return null;
        }
        return (root, query, builder) -> root.get("productId").in(productIds);
    }

    public static Specification<ExtractEntity> filterByUserIds(List<String> userIds) {
        if (CollectionUtils.isEmpty(userIds)) {
            return null;
        }
        return (root, query, builder) -> root.get("user").in(userIds);
    }
}