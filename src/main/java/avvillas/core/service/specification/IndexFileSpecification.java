package avvillas.core.service.specification;

import avvillas.core.constant.fields.IndexFileFieldNames;
import avvillas.core.persistence.entity.IndexFileEntity;
import org.springframework.data.jpa.domain.Specification;

public class IndexFileSpecification {

    private IndexFileSpecification() {}

    public static Specification<IndexFileEntity> filterByProductId(String search) {
        return SpecificationUtils.likeIgnoreCase(IndexFileFieldNames.PRODUCT_ID.getFieldName(), search);
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

    public static Specification<IndexFileEntity> filterByUser(String search) {
        return SpecificationUtils.likeIgnoreCase(IndexFileFieldNames.USER.getFieldName(), search);
    }
}