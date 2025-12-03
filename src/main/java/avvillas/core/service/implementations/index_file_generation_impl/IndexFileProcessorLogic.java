package avvillas.core.service.implementations.index_file_generation_impl;

import avvillas.core.constant.MessageConstant;
import avvillas.core.constant.SubjectConstant;
import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.constant.message.IndexFileMessage;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.persistence.entity.LoadFilesEntryEntity;
import avvillas.core.persistence.entity.ProcessDataEntity;
import avvillas.core.persistence.repository.IndexFileRepository;
import avvillas.core.persistence.repository.LoadFilesEntryRepository;
import avvillas.core.persistence.repository.ProcessDataRepository;
import avvillas.core.service.EmailNotificationService;
import avvillas.core.service.dto.content_index_file.ContentIndexFileDto;
import avvillas.core.service.dto.content_index_file.RequiredFieldsResDto;
import avvillas.core.service.dto.path_index_file.PathExtractsArchiveIndexDto;
import avvillas.core.web.controller.exception.GlobalExceptionHandler;
import avvillas.core.web.traits.LoaderTrait;
import com.fasterxml.jackson.databind.ObjectMapper;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

import java.io.BufferedWriter;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;

import java.util.List;
import java.util.Optional;
import java.util.ArrayList;
import java.util.Map;
import java.util.Objects;
import java.util.NoSuchElementException;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.stream.IntStream;

import static avvillas.core.constant.message.LoadFilesEntryMessage.*;

@Component
@RequiredArgsConstructor
@Slf4j
public class IndexFileProcessorLogic {

    private final IndexFileRepository indexFileRepository;
    private final LoadFilesEntryRepository loadFilesEntryRepository;
    private final ProcessDataRepository processDataRepository;
    private final EmailNotificationService emailNotificationService;
    private final LoaderTrait productTrait;

    private static final String ERROR = "ERROR";

    @Value("${EXIT_ROUTE}")
    private String exitRoute;

    private final ConcurrentHashMap<String, Integer> lastProgressCache = new ConcurrentHashMap<>();

    public void process(String indexFileId, String productId, String period, PathExtractsArchiveIndexDto path, int maxRecordsPerFile, String token) {
        try {

            ObjectMapper objectMapper = new ObjectMapper();

            ContentIndexFileDto content = productTrait.getContentFileByProductId(productId, token);

            LoadFilesEntryEntity loadFilesEntry = loadFilesEntryRepository.findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(productId, period, LoadStatus.FINALIZADO);

            String productName = productTrait.getProductById(productId, token).getDescription();

            if (loadFilesEntry == null) {
                sendNotification(indexFileId, productName, ERROR, MessageConstant.format(LOAD_FILE_ENTRY_NOT_FOUND, productName));
                throw new GlobalExceptionHandler.GlobalMessageException((MessageConstant.format(LOAD_FILE_ENTRY_NOT_FOUND, productName)));
            }

            String processId = loadFilesEntry.getId();

            int clientsProcessed = loadFilesEntry.getClientsProcessed();

            Optional<List<ProcessDataEntity>> processData = processDataRepository.findByProcessId(processId);

            if (processData.isEmpty()) {
                sendNotification(indexFileId, productName, ERROR, MessageConstant.format(LOAD_FILE_ENTRY_NOT_FOUND, productName));
                throw new GlobalExceptionHandler.GlobalMessageException((MessageConstant.format(LOAD_FILE_ENTRY_NOT_FOUND, productName)));
            }

            Path indicesDir = Paths.get("indices");

            if (!Files.exists(indicesDir)) {
                Files.createDirectory(indicesDir);
            }

            if (processData.get().isEmpty()) {
                sendNotification(indexFileId, productName, ERROR, MessageConstant.format(CLIENT_DATA_NOT_FOUND, productName));
                log.error(MessageConstant.format(CLIENT_DATA_NOT_FOUND, productName));
                updateProgress(indexFileId, 100, processId, clientsProcessed);
                return;
            }

            List<ProcessDataEntity> clientRecords = processData.get();
            List<RequiredFieldsResDto> requiredFields = content.getRequiredFields();
            int totalRecords = clientRecords.size();
            int totalRecordsLoadFilesEntry = loadFilesEntry.getClientsProcessed();

            if (totalRecords != totalRecordsLoadFilesEntry) {
                sendNotification(indexFileId, productName, ERROR, String.format(IndexFileMessage.ERROR_INDEX_FILE_TOTAL_DATA, totalRecordsLoadFilesEntry, totalRecords));
                throw new GlobalExceptionHandler.GlobalMessageException(String.format(IndexFileMessage.ERROR_INDEX_FILE_TOTAL_DATA, totalRecordsLoadFilesEntry, totalRecords));
            }

            log.info("Total client records to process: {}", totalRecords);

            int totalFiles = (int) Math.ceil((double) totalRecords / maxRecordsPerFile);
            log.info("Will create {} index files with max {} records per file", totalFiles, maxRecordsPerFile);

            AtomicInteger processedRecords = new AtomicInteger(0);

            int progressUpdateInterval = Math.max(1, totalRecords / 20);

            IntStream.range(0, totalFiles)
                    .parallel()
                    .forEach(fileIndex -> {
                        int startIndex = fileIndex * maxRecordsPerFile;
                        int endIndex = Math.min(startIndex + maxRecordsPerFile, totalRecords);
                        int recordsInThisFile = endIndex - startIndex;

                        String fileNumber = String.format("%03d", fileIndex + 1);
                        String fileName = content.getNameIndexFile() + "_" + fileNumber;

                        String fileExtension = (content.getTypeFile() == ContentIndexFileDto.TypeFile.CSV) ? ".csv" : ".txt";
                        Path filePath = indicesDir.resolve(fileName + fileExtension);
                        log.info("Creating index file {} of {}: {}", (fileIndex + 1), totalFiles, filePath.toAbsolutePath());

                        List<String> formattedRecords = new ArrayList<>(recordsInThisFile);

                        IntStream.range(startIndex, endIndex)
                                .parallel()
                                .forEach(i -> {
                                    ProcessDataEntity eachRecord = clientRecords.get(i);
                                    if (eachRecord.getData() != null) {
                                        try {
                                            String dataString = new String(eachRecord.getData(), StandardCharsets.UTF_8);
                                            Map<String, Object> dataMap = objectMapper.readValue(dataString, Map.class);
                                            StringBuilder pdfNameBuilder = new StringBuilder();

                                            if (requiredFields != null && !requiredFields.isEmpty()) {
                                                for (RequiredFieldsResDto field : requiredFields) {
                                                    String fieldValue = "";

                                                    if (field.getIsFixed() != null && field.getIsFixed()) {
                                                        if (Objects.equals(field.getContent(), exitRoute)) {

                                                            String routeOutputExtract = path.getRouteOutputExtract();
                                                            if (!routeOutputExtract.endsWith("/"))
                                                                routeOutputExtract = routeOutputExtract + "/";

                                                            pdfNameBuilder.append(";").append(routeOutputExtract);
                                                            pdfNameBuilder.append(dataMap.get("fileName").toString()).append(".pdf");
                                                        } else {
                                                            fieldValue = field.getContent();
                                                        }
                                                    } else if (field.getInputStructureProduct() != null) {
                                                        String fieldId = field.getInputStructureProduct().getId();
                                                        String fieldName = field.getInputStructureProduct().getFieldName();

                                                        if (dataMap.containsKey(fieldId)) {
                                                            Object value = dataMap.get(fieldId);
                                                            if (value instanceof List && !((List<?>) value).isEmpty()) {
                                                                fieldValue = ((List<?>) value).get(0).toString();
                                                            } else {
                                                                fieldValue = value.toString();
                                                            }
                                                        } else {
                                                            log.warn("Warning: Field {} not found in data for client ID: {}", fieldName, eachRecord.getClientId());
                                                        }
                                                    }
                                                    if (!pdfNameBuilder.isEmpty() && !fieldValue.isEmpty())
                                                        pdfNameBuilder.append(";");
                                                    pdfNameBuilder.append(fieldValue);
                                                }
                                            }

                                            synchronized (formattedRecords) {
                                                formattedRecords.add(pdfNameBuilder.toString());
                                            }
                                        } catch (Exception e) {
                                            log.warn("Warning: Error processing data for client ID: {} - {}", eachRecord.getClientId(), e.getMessage());
                                            synchronized (formattedRecords) {
                                                formattedRecords.add("");
                                            }
                                        }
                                    } else {
                                        log.warn("Warning: Skipping null data for client ID: {}", eachRecord.getClientId());
                                        synchronized (formattedRecords) {
                                            formattedRecords.add("");
                                        }
                                    }

                                    int processed = processedRecords.incrementAndGet();
                                    if (processed % progressUpdateInterval == 0) {
                                        int progress = (int) (((double) processed / totalRecords) * 100);
                                        updateProgress(indexFileId, progress, "", 0);
                                    }
                                });

                        try {
                            try (BufferedWriter writer = new BufferedWriter(
                                    new OutputStreamWriter(new FileOutputStream(filePath.toFile()), StandardCharsets.UTF_8))) {
                                for (String eachRecord : formattedRecords) {
                                    writer.write(eachRecord);
                                    writer.newLine();
                                }
                                writer.flush();
                            }

                            log.info("Successfully created index file: {}", filePath.getFileName());
                            log.info("Expected record count: {}", recordsInThisFile);
                            log.info("Actual record count: {}", formattedRecords.size());

                            if (formattedRecords.size() != recordsInThisFile) {
                                log.warn("WARNING: Actual record count does not match expected count!");
                            } else {
                                log.info("Verification successful: File contains the expected number of records.");
                            }
                        } catch (IOException e) {
                            log.error("Error writing file {}: {}", filePath, e.getMessage());
                        }
                    });

            try {
                Thread.sleep(500);
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
            }

            updateProgress(indexFileId, 100, processId, clientsProcessed);
            sendNotification(indexFileId, productName, "FINALIZADO", String.format(IndexFileMessage.PROCESS_INDEX_FINALIZED, productName, totalRecordsLoadFilesEntry, totalRecords));
            log.info("Index file generation completed successfully. Created {} files with {} total records.", totalFiles, totalRecords);

        } catch (Exception e) {
            String errorMsg = "Error creating index files: " + e.getMessage();
            log.error(errorMsg);
            updateProgress(indexFileId, -1, "", 0);
        }
    }

    private void sendNotification(String processId, String productName, String status, String details) {
        try {
            indexFileRepository.findById(processId).ifPresent(process -> emailNotificationService.sendProcessStatusNotification(productName, status, details, SubjectConstant.SUBJECT_INDEX));
        } catch (Exception e) {
            log.error("Fallo al intentar enviar la notificación por correo para el proceso {}: {}", processId, e.getMessage());
        }
    }

    private void updateProgress(String indexFileId, int progress, String processId, int clientsProcessed) {
        try {
            Integer lastProgress = lastProgressCache.getOrDefault(indexFileId, -1);

            if (progress == 100 || progress == -1 || Math.abs(progress - lastProgress) >= 5) {
                IndexFileEntity entity = indexFileRepository.findById(indexFileId)
                        .orElseThrow(() -> new NoSuchElementException(MessageConstant.format(IndexFileMessage.INDEX_FILE_NOT_FOUND, indexFileId)));
                entity.setPercentAdvance(progress);

                if (progress == -1) entity.setStatus(LoadStatus.ERROR);

                if (progress == 100) {
                    entity.setProcessId(processId);
                    entity.setClientsProcessed(clientsProcessed);
                    entity.setStatus(LoadStatus.FINALIZADO);
                }

                indexFileRepository.save(entity);

                lastProgressCache.put(indexFileId, progress);
            }
        } catch (Exception e) {
            log.error("Error updating progress: {}", e.getMessage());
        }
    }
}
