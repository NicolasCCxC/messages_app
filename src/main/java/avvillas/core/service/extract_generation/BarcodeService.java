package avvillas.core.service.extract_generation;

import java.io.IOException;

public interface BarcodeService {

    String generateCode128BarcodeAsBase64(String barcodeText) throws IOException;
}