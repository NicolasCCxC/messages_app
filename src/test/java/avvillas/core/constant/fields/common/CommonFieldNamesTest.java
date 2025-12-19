package avvillas.core.constant.fields.common;

import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import static org.assertj.core.api.Assertions.assertThat;

class CommonFieldNamesTest {

    @Test
    @DisplayName("Verifica que el getter del enum devuelva el nombre de campo correcto")
    void shouldReturnCorrectFieldName() {
        String fieldName = CommonFieldNames.ACTIVE.getFieldName();

        assertThat(fieldName).isEqualTo("active");
    }

    @Test
    @DisplayName("Verifica que el método valueOf funcione")
    void shouldCoverValueOf() {
        CommonFieldNames active = CommonFieldNames.valueOf("ACTIVE");

        assertThat(active).isEqualTo(CommonFieldNames.ACTIVE);
    }
}