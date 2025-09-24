package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.constant.message.ExtractMessage;
import avvillas.core.service.EmailNotificationService;
import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.extract_generation.ClientDataService;
import avvillas.core.service.extract_generation.ExtractProcessorService;
import avvillas.core.service.extract_generation.HtmlTemplateService;
import avvillas.core.service.extract_generation.PdfGenerationService;
import avvillas.core.service.extract_generation.ProcessStateService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import avvillas.core.service.implementations.extract_generation_impl.config.InMemoryPdf;
import avvillas.core.web.traits.FormatTrait;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Service;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.Duration;
import java.time.Instant;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.Executor;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.concurrent.atomic.AtomicBoolean;
import java.util.concurrent.atomic.AtomicLong;
import java.util.stream.IntStream;

@Service
public class ExtractProcessorServiceImpl implements ExtractProcessorService {

    private static final Logger logger = LoggerFactory.getLogger(ExtractProcessorServiceImpl.class);

    private final Executor producerExecutor;
    private final Executor consumerExecutor;
    private final ExtractProperties extractProperties;
    private final ClientDataService clientDataService;
    private final HtmlTemplateService htmlTemplateService;
    private final PdfGenerationService pdfGenerationService;
    private final ProcessStateService processStateService;
    private final FormatTrait formatTrait;
    private final EmailNotificationService emailNotificationService;

    public ExtractProcessorServiceImpl(
            @Qualifier("producerTaskExecutor") Executor producerExecutor,
            @Qualifier("consumerTaskExecutor") Executor consumerExecutor,
            ExtractProperties extractProperties,
            ClientDataService clientDataService,
            HtmlTemplateService htmlTemplateService,
            PdfGenerationService pdfGenerationService,
            ProcessStateService processStateService,
            FormatTrait formatTrait,
            EmailNotificationService emailNotificationService) {
        this.producerExecutor = producerExecutor;
        this.consumerExecutor = consumerExecutor;
        this.extractProperties = extractProperties;
        this.clientDataService = clientDataService;
        this.htmlTemplateService = htmlTemplateService;
        this.pdfGenerationService = pdfGenerationService;
        this.processStateService = processStateService;
        this.formatTrait = formatTrait;
        this.emailNotificationService = emailNotificationService;
    }

    @Override
    @Async("consumerTaskExecutor")
    public void processExtractsAsync(String extractId, String productId, String processId, String routeExitExtract, int clientsProcessed, String productName, String token) {
        Instant startProcess = Instant.now();
        logger.info("[PASO 2][ID: {}] Proceso asíncrono iniciado. Hilo: {}. Total de clientes a procesar: {}",
                extractId, Thread.currentThread().getName(), clientsProcessed);

        final AtomicBoolean isProcessFailed = new AtomicBoolean(false);
        final int consumerCount = extractProperties.getPerformance().getConsumerPool().getMaxSize();
        BlockingQueue<Map<String, Object>> clientDataQueue = new LinkedBlockingQueue<>(extractProperties.getPerformance().getQueueCapacity());

        try {
            if (clientsProcessed == 0) throw new IllegalStateException(ExtractMessage.EXTRACT_ERROR_NOT_DATA);

            validateOutputDirectory(extractId, routeExitExtract, clientsProcessed);

            final FormatDto format = formatTrait.getFormatByProductId(productId, token);

            if (format.getHtmlContent() == null)
                throw new IllegalStateException("El formato no cuenta con una plantilla HTML configurada");
            
            final String baseHtmlTemplate = format.getHtmlContent();

            CompletableFuture.runAsync(() -> {
                Thread.currentThread().setName("Extract-Producer-" + extractId);
                clientDataService.produceClientData(processId, clientDataQueue, consumerCount, isProcessFailed);
            }, producerExecutor);

            final AtomicLong processedCounter = new AtomicLong(0);

            Runnable consumerTask = () -> {
                try {
                    Thread.currentThread().setName("Extract-Consumer-" + Thread.currentThread().getId());

                    final int BATCH_SIZE = extractProperties.getPerformance().getPdfWriteBatchSize();
                    List<InMemoryPdf> pdfBatch = new ArrayList<>(BATCH_SIZE);

                    try {
                        while (true) {

                            if (isProcessFailed.get()) {
                                logger.warn("Consumidor {} deteniendose por senal de fallo.", Thread.currentThread().getName());
                                break;
                            }

                            Map<String, Object> clientData = clientDataQueue.take();
                            if (clientData == ClientDataServiceImpl.END_OF_QUEUE) break;

                            Instant startClient = Instant.now();

                            Instant startHtml = Instant.now();
                            String finalHtml = htmlTemplateService.prepareClientHtml(baseHtmlTemplate, clientData, format);
                            long htmlDuration = Duration.between(startHtml, Instant.now()).toMillis();

                            Instant startPdf = Instant.now();

                            byte[] pdfBytes = pdfGenerationService.generatePdf(clientData, finalHtml);
                            long pdfDuration = Duration.between(startPdf, Instant.now()).toMillis();

                            if (pdfBytes != null && pdfBytes.length > 0) {
                                String fileName = clientData.getOrDefault("fileName", "extract-" + System.currentTimeMillis()) + ".pdf";
                                pdfBatch.add(new InMemoryPdf(fileName, pdfBytes));
                            }

                            if (pdfBatch.size() >= BATCH_SIZE) {
                                writeBatchToDisk(extractId, routeExitExtract, pdfBatch);
                                pdfBatch.clear();
                            }

                            long totalClientDuration = Duration.between(startClient, Instant.now()).toMillis();
                            logger.debug("[PASO 3][ID: {}] Cliente procesado en {}ms (HTML: {}ms, PDF: {}ms)",
                                    extractId, totalClientDuration, htmlDuration, pdfDuration);

                            long currentCount = processedCounter.incrementAndGet();
                            processStateService.updateProgress(extractId, currentCount, clientsProcessed);
                        }
                    } catch (InterruptedException e) {
                        Thread.currentThread().interrupt();
                    } catch (Exception e) {
                        logger.error("FALLO CRITICO EN CONSUMIDOR {}. Deteniendo todo el proceso.", Thread.currentThread().getName(), e);
                        isProcessFailed.set(true);
                    } finally {
                        if (!pdfBatch.isEmpty()) {
                            writeBatchToDisk(extractId, routeExitExtract, pdfBatch);
                            pdfBatch.clear();
                        }
                    }
                } finally {
                    PdfGenerationServiceImpl.cleanupThread();
                }
            };

            List<CompletableFuture<Void>> consumerFutures = IntStream.range(0, consumerCount)
                    .mapToObj(i -> CompletableFuture.runAsync(consumerTask, consumerExecutor))
                    .toList();

            CompletableFuture.allOf(consumerFutures.toArray(new CompletableFuture[0])).join();

            if (isProcessFailed.get()) {
                throw new RuntimeException("El proceso de generacion de extractos fallo. Revisar logs para mas detalles.");
            }

            long totalDurationSeconds = Duration.between(startProcess, Instant.now()).toSeconds();
            long totalProcessed = processedCounter.get();

            double avgTimePerPdf = (totalProcessed > 0) ? (double) (totalDurationSeconds * 1000) / totalProcessed : 0;
            String formattedAvgTime = String.format("%.2f", avgTimePerPdf);
            logger.info("[PASO 4][ID: {}] Proceso FINALIZADO CON ÉXITO. {} de {} extractos generados en {}s (Promedio: {} ms/extracto).",
                    extractId, totalProcessed, clientsProcessed, totalDurationSeconds, formattedAvgTime);

            processStateService.markAsCompleted(extractId, "Proceso completado. " + processedCounter.get() + " de " + clientsProcessed + " extractos generados.");

            emailNotificationService.sendProcessStatusNotification(
                    productName,
                    ExtractMessage.FINISH,
                    ExtractMessage.EXTRACT_FINISH,
                    ExtractMessage.SUBJECT_EXTRACTS
            );

        } catch (Exception e) {
            long errorDurationSeconds = Duration.between(startProcess, Instant.now()).toSeconds();

            logger.error("[PASO 4][ID: {}] Proceso FINALIZADO CON ERROR después de {}s. Causa: {}",
                    extractId, errorDurationSeconds, e.getMessage());
            String errorMessage = e.getMessage();
            processStateService.markAsFailed(extractId, errorMessage);

            emailNotificationService.sendProcessStatusNotification(
                    productName,
                    ExtractMessage.ERROR,
                    String.format(ExtractMessage.EXTRACT_ERROR, errorMessage),
                    ExtractMessage.SUBJECT_EXTRACTS
            );
        }
    }

    private void writeBatchToDisk(String extractId, String outputPath, List<InMemoryPdf> pdfBatch) {
        Instant startWrite = Instant.now();
        for (InMemoryPdf pdf : pdfBatch) {
            try {
                Path filePath = Paths.get(outputPath, pdf.fileName());
                Files.write(filePath, pdf.content());
            } catch (IOException e) {
                logger.error("[{}] Fallo al escribir el archivo {} en disco.", extractId, pdf.fileName(), e);
            }
        }
        long writeDuration = Duration.between(startWrite, Instant.now()).toMillis();
        logger.info("[{}] Lote de {} PDFs escrito en disco en {}ms.", extractId, pdfBatch.size(), writeDuration);
    }


    private void validateOutputDirectory(String extractId, String outputPath, int clientsToProcess) throws IOException {
        if (outputPath == null || outputPath.isBlank()) {
            throw new IllegalArgumentException("La ruta de salida no puede ser nula o vacía.");
        }

        Path directory = Paths.get(outputPath);

        if (!Files.exists(directory)) {
            Files.createDirectories(directory);
        } else if (!Files.isDirectory(directory)) {
            throw new IOException("La ruta de salida '" + outputPath + "' existe pero no es un directorio.");
        }

        long requiredSpaceBytes = (long) clientsToProcess * extractProperties.getPerformance().getPdfMaxSizeKb() * 1024;
        long usableSpaceBytes = Files.getFileStore(directory).getUsableSpace();

        if (usableSpaceBytes < requiredSpaceBytes) {
            String requiredSpaceMB = String.format("%.2f MB", requiredSpaceBytes / (1024.0 * 1024.0));
            String usableSpaceMB = String.format("%.2f MB", usableSpaceBytes / (1024.0 * 1024.0));
            throw new IOException(
                    String.format("Espacio insuficiente en disco. Requerido: ~%s, Disponible: %s.", requiredSpaceMB, usableSpaceMB)
            );
        }

        logger.info("[{}] Validación de espacio en disco exitosa. (Requerido: ~{} MB, Disponible: {} MB)",
                extractId,
                (long) (requiredSpaceBytes / (1024.0 * 1024.0)),
                (long) (usableSpaceBytes / (1024.0 * 1024.0))
        );
    }
}