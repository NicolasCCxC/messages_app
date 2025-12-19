package avvillas.core.service.dto.content_index_file;

import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import static org.assertj.core.api.Assertions.assertThat;

class InputProductStructureFieldNameResDtoTest {

    @Test
    @DisplayName("Verifica el constructor, getters, equals, hashCode y toString")
    void testRecordMethods() {
        InputProductStructureFieldNameResDto dto1 = new InputProductStructureFieldNameResDto(
                "id1", "fieldName1", "identifier1"
        );

        assertThat(dto1.getId()).isEqualTo("id1");
        assertThat(dto1.getFieldName()).isEqualTo("fieldName1");
        assertThat(dto1.getIndexFileIdentifier()).isEqualTo("identifier1");

        InputProductStructureFieldNameResDto dto2 = new InputProductStructureFieldNameResDto(
                "id1", "fieldName1", "identifier1"
        );

        assertThat(dto1)
                .isEqualTo(dto2)
                .hasSameHashCodeAs(dto2);

        InputProductStructureFieldNameResDto dto3 = new InputProductStructureFieldNameResDto(
                "id2", "fieldName2", "identifier2"
        );

        assertThat(dto1).isNotEqualTo(dto3);
        assertThat(dto1.hashCode()).isNotEqualTo(dto3.hashCode());

        assertThat(dto1.toString()).contains("id1", "fieldName1", "identifier1");
    }
}