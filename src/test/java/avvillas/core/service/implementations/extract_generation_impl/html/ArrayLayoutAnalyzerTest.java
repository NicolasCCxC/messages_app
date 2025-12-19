package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.common.TestDataFactory;
import avvillas.core.service.dto.format.FormatDto;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

class ArrayLayoutAnalyzerTest {

    private ArrayLayoutAnalyzer analyzer;
    private FormatDto.PageResponse pageConfig;
    private Map<String, Object> clientData;

    @BeforeEach
    void setUp() {
        analyzer = new ArrayLayoutAnalyzer();
        pageConfig = new FormatDto.PageResponse();
        clientData = new HashMap<>();
    }

    @Test
    @DisplayName("Verifica que retorne vacío si la configuración de elementos es nula")
    void findArrayColumnGroup_shouldReturnEmptyWhenElementsAreNull() {
        pageConfig.setElements(null);
        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertTrue(result.isEmpty());
    }

    @Test
    @DisplayName("Verifica que retorne vacío si la configuración de elementos está vacía")
    void findArrayColumnGroup_shouldReturnEmptyWhenElementsAreEmpty() {
        pageConfig.setElements(Collections.emptyList());
        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertTrue(result.isEmpty());
    }

    @Test
    @DisplayName("Verifica que los elementos con objectId sean filtrados")
    void findArrayColumnGroup_shouldFilterElementsWithObjectId() {
        FormatDto.ElementResponse el = TestDataFactory.mockElementResponse("field1", 100);
        el.setObjectId("some-object-id");

        pageConfig.setElements(List.of(el));
        clientData.put("field1", List.of("data"));

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertTrue(result.isEmpty());
    }

    @Test
    @DisplayName("Verifica que los elementos con fieldId nulo o vacío sean filtrados")
    void findArrayColumnGroup_shouldFilterElementsWithNullOrEmptyFieldId() {
        FormatDto.ElementResponse elNull = TestDataFactory.mockElementResponse(null, 100);
        FormatDto.ElementResponse elEmpty = TestDataFactory.mockElementResponse("", 100);

        pageConfig.setElements(List.of(elNull, elEmpty));

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertTrue(result.isEmpty());
    }

    @Test
    @DisplayName("Verifica que retorne vacío si ningún grupo tiene datos de array")
    void findArrayColumnGroup_shouldReturnEmptyIfNoGroupHasArrayData() {
        FormatDto.ElementResponse el1 = TestDataFactory.mockElementResponse("field1", 100);
        FormatDto.ElementResponse el2 = TestDataFactory.mockElementResponse("field2", 102);

        pageConfig.setElements(List.of(el1, el2));
        clientData.put("field1", "Not a List");
        clientData.put("field2", 12345);

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertTrue(result.isEmpty());
    }

    @Test
    @DisplayName("Verifica que agrupe elementos dentro de la tolerancia de píxeles")
    void findArrayColumnGroup_shouldGroupElementsWithinPixelTolerance() {
        FormatDto.ElementResponse el1 = TestDataFactory.mockElementResponse("field1", 100);
        FormatDto.ElementResponse el2 = TestDataFactory.mockElementResponse("field2", 105);

        pageConfig.setElements(List.of(el1, el2));
        clientData.put("field1", List.of("data"));

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertEquals(2, result.size());
        assertTrue(result.contains(el1));
        assertTrue(result.contains(el2));
    }

    @Test
    @DisplayName("Verifica que separe elementos fuera de la tolerancia de píxeles")
    void findArrayColumnGroup_shouldSeparateElementsOutsidePixelTolerance() {
        FormatDto.ElementResponse el1 = TestDataFactory.mockElementResponse("field1", 100);
        FormatDto.ElementResponse el2 = TestDataFactory.mockElementResponse("field2", 106);

        pageConfig.setElements(List.of(el1, el2));
        clientData.put("field1", List.of("data"));
        clientData.put("field2", List.of("data"));

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertEquals(1, result.size());
        assertTrue(result.contains(el1));
    }

    @Test
    @DisplayName("Verifica que seleccione el grupo más grande si ambos tienen datos de array")
    void findArrayColumnGroup_shouldSelectLargestGroupWithArrayData() {
        FormatDto.ElementResponse el11 = TestDataFactory.mockElementResponse("field1_1", 100);
        FormatDto.ElementResponse el12 = TestDataFactory.mockElementResponse("field1_2", 102);

        FormatDto.ElementResponse el21 = TestDataFactory.mockElementResponse("field2_1", 200);
        FormatDto.ElementResponse el22 = TestDataFactory.mockElementResponse("field2_2", 201);
        FormatDto.ElementResponse el23 = TestDataFactory.mockElementResponse("field2_3", 204);

        pageConfig.setElements(List.of(el11, el12, el21, el22, el23));
        clientData.put("field1_1", List.of("data1"));
        clientData.put("field2_1", List.of("data2"));

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertEquals(3, result.size());
        assertTrue(result.contains(el21));
        assertTrue(result.contains(el22));
        assertTrue(result.contains(el23));
    }

    @Test
    @DisplayName("Verifica que seleccione el único grupo con datos de array, aunque no sea el más grande")
    void findArrayColumnGroup_shouldSelectOnlyGroupWithArrayData() {
        FormatDto.ElementResponse el11 = TestDataFactory.mockElementResponse("field1_1", 100);
        FormatDto.ElementResponse el12 = TestDataFactory.mockElementResponse("field1_2", 102);

        FormatDto.ElementResponse el21 = TestDataFactory.mockElementResponse("field2_1", 200);

        pageConfig.setElements(List.of(el11, el12, el21));
        clientData.put("field1_1", "string data");
        clientData.put("field2_1", List.of("list data"));

        List<FormatDto.ElementResponse> result = analyzer.findArrayColumnGroup(pageConfig, clientData);
        assertEquals(1, result.size());
        assertTrue(result.contains(el21));
    }
}