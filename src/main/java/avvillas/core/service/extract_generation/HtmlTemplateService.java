package avvillas.core.service.extract_generation;

import avvillas.core.service.dto.format.FormatDto;

import java.util.Map;

public interface HtmlTemplateService {

    String prepareClientHtml(String baseHtmlTemplate, Map<String, Object> clientData, FormatDto formatDto);
}