package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.extract_generation.HtmlTemplateService;
import avvillas.core.service.implementations.extract_generation_impl.html.ArrayLayoutAnalyzer;
import avvillas.core.service.implementations.extract_generation_impl.html.HtmlElementRenderer;
import avvillas.core.service.implementations.extract_generation_impl.html.HtmlStructureExtractor;
import avvillas.core.service.implementations.extract_generation_impl.html.RowDataDistributor;
import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@Service
@RequiredArgsConstructor
public class HtmlTemplateServiceImpl implements HtmlTemplateService {

    private static final Logger logger = LoggerFactory.getLogger(HtmlTemplateServiceImpl.class);
    private final HtmlStructureExtractor structureExtractor;
    private final ArrayLayoutAnalyzer layoutAnalyzer;
    private final RowDataDistributor rowDataDistributor;
    private final HtmlElementRenderer elementRenderer;

    @Override
    public String prepareClientHtml(String baseHtmlTemplate, Map<String, Object> clientData, FormatDto formatDto) {
        String initialHtml = replacePlaceholders(baseHtmlTemplate, clientData);

        HtmlStructureExtractor.HtmlPageStructure structure = structureExtractor.extract(initialHtml);

        List<FormatDto.ElementResponse> firstPageGroup = layoutAnalyzer.findArrayColumnGroup(formatDto.getPages().get(0), clientData);

        if (firstPageGroup.isEmpty()) {
            logger.warn("No se encontro un grupo de columnas de array valido.");
            String staticElements = elementRenderer.generateForPage(clientData, formatDto.getPages().get(0), null, new ArrayList<>());
            return structure.htmlHead() + injectElementsIntoPageDiv(structure.firstPageTemplate(), staticElements) + structure.htmlFoot();
        }

        RowDataDistributor.ArrayDistribution distribution = rowDataDistributor.calculate(clientData, formatDto, firstPageGroup);

        boolean isContinuesMode = formatDto.getPdfConfig() != null && Boolean.TRUE.equals(formatDto.getPdfConfig().getContinues());

        int totalPagesInFormat = formatDto.getPages().size();
        int pagesToRender = isContinuesMode
                ? distribution.totalPagesNeeded()
                : Math.min(distribution.totalPagesNeeded(), totalPagesInFormat);
        StringBuilder finalHtml = new StringBuilder(structure.htmlHead());
        for (int pageNum = 1; pageNum <= pagesToRender; pageNum++) {
            boolean isFirstPage = (pageNum == 1);

            String pageTemplate = (isFirstPage || structure.continuationPageTemplate().isEmpty())
                    ? structure.firstPageTemplate()
                    : structure.continuationPageTemplate();

            FormatDto.PageResponse pageConfig = formatDto.getPages().get(isFirstPage ? 0 : 1);

            List<FormatDto.ElementResponse> currentPageGroup = isFirstPage
                    ? firstPageGroup
                    : layoutAnalyzer.findArrayColumnGroup(pageConfig, clientData);

            List<Map<String, String>> rowsForThisPage = distribution.rowsByPage().get(pageNum);

            String positionedElements = elementRenderer.generateForPage(clientData, pageConfig, rowsForThisPage, currentPageGroup);
            finalHtml.append(injectElementsIntoPageDiv(pageTemplate, positionedElements));
        }

        finalHtml.append(structure.htmlFoot());
        return finalHtml.toString();
    }

    private String replacePlaceholders(String template, Map<String, Object> data) {
        String result = template;
        for (Map.Entry<String, Object> entry : data.entrySet()) {
            String placeholder = "{{" + entry.getKey() + "}}";
            Object value = entry.getValue();

            if (!(value instanceof List)) {
                String stringValue = (value != null) ? value.toString() : "";
                result = result.replace(placeholder, stringValue);
            }
        }
        return result;
    }

    private String injectElementsIntoPageDiv(String pageTemplate, String elements) {
        int closingDivIndex = pageTemplate.lastIndexOf("</div>");
        if (closingDivIndex != -1) {
            return new StringBuilder(pageTemplate).insert(closingDivIndex, elements).toString();
        }
        return pageTemplate + elements;
    }

}