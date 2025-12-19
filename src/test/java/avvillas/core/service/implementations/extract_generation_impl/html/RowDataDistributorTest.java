package avvillas.core.service.implementations.extract_generation_impl.html;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.common.TestDataFactory;
import avvillas.core.service.dto.format.FormatDto;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.InjectMocks;
import org.mockito.Mock;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.never;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

class RowDataDistributorTest extends BaseServiceTest {

    @Mock
    private ArrayLayoutAnalyzer layoutAnalyzer;

    @InjectMocks
    private RowDataDistributor rowDataDistributor;

    private List<FormatDto.ElementResponse> columnGroup;
    private Map<String, Object> clientData;

    private FormatDto mockFormat;
    private List<FormatDto.ElementResponse> firstPageColumnGroup;
    private List<FormatDto.ElementResponse> contPageColumnGroup;
    private FormatDto.PageResponse firstPage;
    private FormatDto.PageResponse contPage;

    @BeforeEach
    void setUp() {
        columnGroup = new ArrayList<>();
        clientData = new HashMap<>();
    }

    private void setUpCalculate() {
        mockFormat = new FormatDto();
        FormatDto.PdfConfigResponse pdfConfig = new FormatDto.PdfConfigResponse();
        pdfConfig.setPaperType("A4");
        mockFormat.setPdfConfig(pdfConfig);

        firstPage = new FormatDto.PageResponse();
        firstPage.setPageNumber(1);
        firstPageColumnGroup = List.of(
                TestDataFactory.mockElementResponse("colA", 100)
        );
        firstPage.setElements(new ArrayList<>(firstPageColumnGroup));

        contPage = new FormatDto.PageResponse();
        contPage.setPageNumber(2);
        contPageColumnGroup = List.of(
                TestDataFactory.mockElementResponse("colA", 50)
        );
        contPage.setElements(new ArrayList<>(contPageColumnGroup));

        mockFormat.setPages(new ArrayList<>(List.of(firstPage, contPage)));
    }

    @Test
    @DisplayName("Verifica la reconstrucción exitosa con columnas de igual tamaño")
    void reconstructRowsFromColumns_shouldHandleEvenColumns() {
        columnGroup.add(TestDataFactory.mockElementResponse("colA", 100));
        columnGroup.add(TestDataFactory.mockElementResponse("colB", 100));

        clientData.put("colA", List.of("A1", "A2"));
        clientData.put("colB", List.of("B1", "B2"));

        List<Map<String, String>> rows = rowDataDistributor.reconstructRowsFromColumns(clientData, columnGroup);

        assertEquals(2, rows.size());
        assertEquals(Map.of("colA", "A1", "colB", "B1"), rows.get(0));
        assertEquals(Map.of("colA", "A2", "colB", "B2"), rows.get(1));
    }

    @Test
    @DisplayName("Verifica el manejo de columnas desiguales (padding con vacíos)")
    void reconstructRowsFromColumns_shouldHandleUnevenColumns() {
        columnGroup.add(TestDataFactory.mockElementResponse("colA", 100));
        columnGroup.add(TestDataFactory.mockElementResponse("colB", 100));

        clientData.put("colA", List.of("A1", "A2", "A3"));
        clientData.put("colB", List.of("B1"));

        List<Map<String, String>> rows = rowDataDistributor.reconstructRowsFromColumns(clientData, columnGroup);

        assertEquals(3, rows.size());
        assertEquals(Map.of("colA", "A1", "colB", "B1"), rows.get(0));
        assertEquals(Map.of("colA", "A2", "colB", ""), rows.get(1));
        assertEquals(Map.of("colA", "A3", "colB", ""), rows.get(2));
    }

    @Test
    @DisplayName("Verifica el manejo de datos que no son listas")
    void reconstructRowsFromColumns_shouldHandleNonListData() {
        columnGroup.add(TestDataFactory.mockElementResponse("colA", 100));
        columnGroup.add(TestDataFactory.mockElementResponse("colB", 100));

        clientData.put("colA", "Soy un String, no una lista");
        clientData.put("colB", List.of("B1"));

        List<Map<String, String>> rows = rowDataDistributor.reconstructRowsFromColumns(clientData, columnGroup);

        assertEquals(1, rows.size());
        assertEquals(Map.of("colA", "", "colB", "B1"), rows.get(0));
    }

    @Test
    @DisplayName("Verifica el manejo de valores nulos dentro de la lista")
    void reconstructRowsFromColumns_shouldHandleNullsInList() {
        columnGroup.add(TestDataFactory.mockElementResponse("colA", 100));
        clientData.put("colA", Arrays.asList("A1", null, "A3"));

        List<Map<String, String>> rows = rowDataDistributor.reconstructRowsFromColumns(clientData, columnGroup);

        assertEquals(3, rows.size());
        assertEquals(Map.of("colA", "A1"), rows.get(0));
        assertEquals(Map.of("colA", ""), rows.get(1));
        assertEquals(Map.of("colA", "A3"), rows.get(2));
    }

    @Test
    @DisplayName("Verifica el manejo de `columnGroup` vacío")
    void reconstructRowsFromColumns_shouldReturnEmptyListWhenGroupIsEmpty() {
        clientData.put("colA", List.of("A1", "A2"));

        List<Map<String, String>> rows = rowDataDistributor.reconstructRowsFromColumns(clientData, Collections.emptyList());

        assertTrue(rows.isEmpty());
    }

    @Test
    @DisplayName("Verifica el manejo de `maxRows` cero")
    void reconstructRowsFromColumns_shouldReturnEmptyListWhenMaxRowsIsZero() {
        columnGroup.add(TestDataFactory.mockElementResponse("colA", 100));
        clientData.put("colA", Collections.emptyList());

        List<Map<String, String>> rows = rowDataDistributor.reconstructRowsFromColumns(clientData, columnGroup);

        assertTrue(rows.isEmpty());
    }

    @Test
    @DisplayName("Verifica la paginación en una sola página (todas las filas caben)")
    void calculate_shouldDistributeAllRowsOnFirstPage() {
        setUpCalculate();

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 10; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(1, result.totalPagesNeeded());
        assertEquals(1, result.rowsByPage().size());
        assertTrue(result.rowsByPage().containsKey(1));
        assertEquals(10, result.rowsByPage().get(1).size());
        verify(layoutAnalyzer, never()).findArrayColumnGroup(eq(contPage), any());
    }

    @Test
    @DisplayName("Verifica la paginación en múltiples páginas (desborde)")
    void calculate_shouldDistributeRowsAcrossMultiplePages() {
        setUpCalculate();
        when(layoutAnalyzer.findArrayColumnGroup(eq(contPage), any())).thenReturn(contPageColumnGroup);

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 60; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(2, result.totalPagesNeeded());
        assertEquals(2, result.rowsByPage().size());
        assertTrue(result.rowsByPage().containsKey(1));
        assertTrue(result.rowsByPage().containsKey(2));
        assertEquals(56, result.rowsByPage().get(1).size());
        assertEquals(4, result.rowsByPage().get(2).size());
        verify(layoutAnalyzer).findArrayColumnGroup(eq(contPage), any());
    }

    @Test
    @DisplayName("Verifica el desborde sin página de continuación (solo P1 definida)")
    void calculate_shouldDiscardRemainingRowsWhenNoContinuationPage() {
        setUpCalculate();
        mockFormat.setPages(List.of(firstPage));

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 60; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(1, result.totalPagesNeeded());
        assertEquals(1, result.rowsByPage().size());
        assertEquals(56, result.rowsByPage().get(1).size());
        verify(layoutAnalyzer, never()).findArrayColumnGroup(eq(contPage), any());
    }

    @Test
    @DisplayName("Verifica el desborde sin grupo de columnas en página de continuación")
    void calculate_shouldDiscardRemainingRowsWhenNoContinuationGroup() {
        setUpCalculate();
        when(layoutAnalyzer.findArrayColumnGroup(eq(contPage), any())).thenReturn(Collections.emptyList());

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 60; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(1, result.totalPagesNeeded());
        assertEquals(1, result.rowsByPage().size());
        assertEquals(56, result.rowsByPage().get(1).size());
        verify(layoutAnalyzer).findArrayColumnGroup(eq(contPage), any());
    }

    @Test
    @DisplayName("Verifica el cálculo del límite por elemento inferior")
    void calculate_shouldSetLimitBasedOnElementBelow() {
        setUpCalculate();
        when(layoutAnalyzer.findArrayColumnGroup(eq(contPage), any())).thenReturn(contPageColumnGroup);

        firstPage.getElements().add(TestDataFactory.mockElementResponse("footer", 500));

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 30; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(2, result.totalPagesNeeded());
        assertEquals(22, result.rowsByPage().get(1).size());
        assertEquals(8, result.rowsByPage().get(2).size());
    }

    @Test
    @DisplayName("Verifica el cálculo del límite por altura de papel (Letter)")
    void calculate_shouldUseLetterPageHeightWhenNoElementBelow() {
        setUpCalculate();
        when(layoutAnalyzer.findArrayColumnGroup(eq(contPage), any())).thenReturn(contPageColumnGroup);
        mockFormat.getPdfConfig().setPaperType("LETTER");

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 55; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(2, result.totalPagesNeeded());
        assertEquals(53, result.rowsByPage().get(1).size());
        assertEquals(2, result.rowsByPage().get(2).size());
    }

    @Test
    @DisplayName("Verifica el cálculo del límite por altura de papel (Legal)")
    void calculate_shouldUseLegalPageHeightWhenNoElementBelow() {
        setUpCalculate();
        when(layoutAnalyzer.findArrayColumnGroup(eq(contPage), any())).thenReturn(contPageColumnGroup);
        mockFormat.getPdfConfig().setPaperType("LEGAL");

        List<String> data = new ArrayList<>();
        for (int i = 0; i < 70; i++) {
            data.add("Row " + i);
        }
        clientData.put("colA", data);

        RowDataDistributor.ArrayDistribution result = rowDataDistributor.calculate(clientData, mockFormat, firstPageColumnGroup);

        assertEquals(2, result.totalPagesNeeded());
        assertEquals(69, result.rowsByPage().get(1).size());
        assertEquals(1, result.rowsByPage().get(2).size());
    }
}