package avvillas.core.common;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.ExtractEntity;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.service.dto.UserNameAndEmailDto;
import avvillas.core.service.dto.extract.ExtractDto;
import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.dto.product.ProductCodeDescriptionDTO;
import avvillas.core.service.dto.product.ProductDTO;

import java.time.LocalDateTime;

public class TestDataFactory {

    public static FormatDto mockFormatDto() {
        FormatDto format = new FormatDto();
        format.setId("format-1");
        format.setProductId("prod-abc");
        format.setActive(true);
        format.setVersion(1);
        format.setHtmlContent("<html>Contenido de prueba: {{name}}</html>");
        return format;
    }

    public static IndexFileEntity mockIndexFileEntity() {
        IndexFileEntity entity = new IndexFileEntity();
        entity.setId("index-uuid-123");
        entity.setProcessId("process-uuid-456");
        entity.setProductId("prod-abc");
        entity.setPeriod("202501");
        entity.setStatus(LoadStatus.FINALIZADO);
        entity.setRoute("/fake/path/output");
        entity.setClientsProcessed(150);
        return entity;
    }

    public static ExtractEntity mockExtractEntity(String productId, String period, LoadStatus status, String userId) {
        ExtractEntity entity = new ExtractEntity();
        entity.setId("extract-uuid-789");
        entity.setProductId(productId);
        entity.setPeriod(period);
        entity.setStatus(status);
        entity.setUser(userId);
        entity.setPercentAdvance(0);
        return entity;
    }

    public static ProductCodeDescriptionDTO mockProductDto() {
        return new ProductCodeDescriptionDTO("prod-abc", "P-100", "Producto de Prueba");
    }

    public static UserNameAndEmailDto mockUserNameAndEmailDto() {
        return new UserNameAndEmailDto("user-uuid-111", "user@test.com", "Usuario Prueba");
    }

    public static ExtractDto mockExtractDtoRequest() {
        ExtractDto dto = new ExtractDto();
        dto.setProductId("prod-abc");
        dto.setPeriod("202501");
        return dto;
    }

    public static ProductDTO mockProductDTO() {
        ProductDTO dto = new ProductDTO();
        dto.setId("prod-abc");
        dto.setCode("P-100");
        dto.setDescription("Producto de Prueba");
        dto.setActive(true);
        dto.setDocumentType("CC");
        dto.setCreatedAt(LocalDateTime.now());
        return dto;
    }

    public static FormatDto.ElementResponse mockElementResponse(String fieldId, int y) {
        FormatDto.ElementResponse element = new FormatDto.ElementResponse();
        element.setFieldId(fieldId);
        element.setPositionY(y);
        return element;
    }
}
