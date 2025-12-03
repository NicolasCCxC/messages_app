package avvillas.core.constant.fields.common;

import lombok.Getter;

@Getter
public enum CommonFieldNames {

    ACTIVE("active");

    private final String fieldName;

    CommonFieldNames(String fieldName) {
        this.fieldName = fieldName;
    }

}
