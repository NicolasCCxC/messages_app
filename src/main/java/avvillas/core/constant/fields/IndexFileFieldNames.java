package avvillas.core.constant.fields;

import lombok.Getter;

@Getter
public enum IndexFileFieldNames {
    STATUS("status"),
    PERIOD("period"),
    PRODUCT_ID("productId"),
    USER("user"),
    PERCENT_ADVANCE("percentAdvance"),
    CREATED_AT("createdAt");

    private final String fieldName;

    IndexFileFieldNames(String fieldName) {
        this.fieldName = fieldName;
    }
}
