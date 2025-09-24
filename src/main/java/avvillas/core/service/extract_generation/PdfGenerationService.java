package avvillas.core.service.extract_generation;

import java.util.Map;

public interface PdfGenerationService {

    byte[] generatePdf(Map<String, Object> clientData, String finalHtml);
}