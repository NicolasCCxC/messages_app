package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.common.TestDataFactory;
import avvillas.core.constant.message.ExtractMessage;
import avvillas.core.service.EmailNotificationService;
import avvillas.core.service.dto.format.FormatDto;
import avvillas.core.service.extract_generation.ClientDataService;
import avvillas.core.service.extract_generation.HtmlTemplateService;
import avvillas.core.service.extract_generation.PdfGenerationService;
import avvillas.core.service.extract_generation.ProcessStateService;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import avvillas.core.web.traits.FormatTrait;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.MockedStatic;
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;

import java.io.IOException;
import java.nio.file.FileStore;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.Map;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.Executor;
import java.util.concurrent.atomic.AtomicBoolean;

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyInt;
import static org.mockito.ArgumentMatchers.anyLong;
import static org.mockito.ArgumentMatchers.anyMap;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.doAnswer;
import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.mockStatic;
import static org.mockito.Mockito.never;
import static org.mockito.Mockito.times;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@MockitoSettings(strictness = Strictness.LENIENT)
class ExtractProcessorServiceImplTest extends BaseServiceTest {

    @Mock
    private Executor producerExecutor;
    @Mock
    private Executor consumerExecutor;
    @Mock
    private ExtractProperties extractProperties;
    @Mock
    private ClientDataService clientDataService;
    @Mock
    private HtmlTemplateService htmlTemplateService;
    @Mock
    private PdfGenerationService pdfGenerationService;
    @Mock
    private ProcessStateService processStateService;
    @Mock
    private FormatTrait formatTrait;
    @Mock
    private EmailNotificationService emailNotificationService;

    @Mock
    private ExtractProperties.Performance performance;
    @Mock
    private ExtractProperties.ConsumerPool consumerPool;
    @Mock
    private Path mockPath;
    @Mock
    private FileStore mockFileStore;

    private MockedStatic<Paths> pathsMock;
    private MockedStatic<Files> filesMock;

    @InjectMocks
    private ExtractProcessorServiceImpl extractProcessorService;

    @BeforeEach
    void setUp() {
        pathsMock = mockStatic(Paths.class);
        filesMock = mockStatic(Files.class);

        when(extractProperties.getPerformance()).thenReturn(performance);
        when(performance.getConsumerPool()).thenReturn(consumerPool);
        when(performance.getQueueCapacity()).thenReturn(100);
    }

    @AfterEach
    void tearDown() {
        pathsMock.close();
        filesMock.close();
    }

    @Test
    @DisplayName("Verifica el procesamiento exitoso de extractos bajo condiciones normales")
    void shouldProcessExtractsSuccessfullyWhenAllConditionsAreMet() throws Exception {
        String extractId = "ext-123";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";

        when(consumerPool.getMaxSize()).thenReturn(2);
        when(performance.getPdfWriteBatchSize()).thenReturn(5);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        pathsMock.when(() -> Paths.get(anyString(), anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        Map<String, Object> client1 = Map.of("name", "Cliente Uno", "fileName", "client1-file");
        Map<String, Object> client2 = Map.of("name", "Cliente Dos", "fileName", "client2-file");

        doAnswer(invocation -> {
            BlockingQueue<Map<String, Object>> queue = invocation.getArgument(1);
            int consumerCount = invocation.getArgument(2);
            queue.put(client1);
            queue.put(client2);
            for (int i = 0; i < consumerCount; i++) {
                queue.put(ClientDataServiceImpl.END_OF_QUEUE);
            }
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        when(htmlTemplateService.prepareClientHtml(anyString(), anyMap(), any(FormatDto.class)))
                .thenReturn("<html>Cliente Uno</html>")
                .thenReturn("<html>Cliente Dos</html>");

        byte[] pdfBytes1 = new byte[]{1, 2, 3};
        byte[] pdfBytes2 = new byte[]{4, 5, 6};

        when(pdfGenerationService.generatePdf(anyMap(), anyString()))
                .thenReturn(pdfBytes1)
                .thenReturn(pdfBytes2);

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(htmlTemplateService, times(2)).prepareClientHtml(anyString(), anyMap(), any(FormatDto.class));
        verify(pdfGenerationService, times(2)).generatePdf(anyMap(), anyString());

        verify(processStateService, times(2)).updateProgress(eq(extractId), anyLong(), eq((long) clientsProcessed));

        filesMock.verify(() -> Files.write(mockPath, pdfBytes1), times(1));
        filesMock.verify(() -> Files.write(mockPath, pdfBytes2), times(1));

        verify(processStateService).markAsCompleted(eq(extractId), anyString());

        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.FINISH),
                eq(ExtractMessage.EXTRACT_FINISH),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );

        verify(processStateService, never()).markAsFailed(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla rápida si el conteo de clientes es cero")
    void shouldFailFastWhenClientCountIsZero() {
        String extractId = "ext-123";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 0;
        String productName = "Producto Test";
        String token = "fake-token";

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        filesMock.verify(() -> Files.exists(any(Path.class)), never());
        verify(formatTrait, never()).getFormatByProductId(anyString(), anyString());

        verify(processStateService).markAsFailed(eq(extractId), eq(ExtractMessage.EXTRACT_ERROR_NOT_DATA));

        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );

        verify(processStateService, never()).markAsCompleted(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si el formato no tiene plantilla HTML")
    void shouldFailWhenHtmlTemplateIsNull() throws Exception {
        String extractId = "ext-456";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";

        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        mockFormat.setHtmlContent(null); // Condición de falla
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), eq("El formato no cuenta con una plantilla HTML configurada"));
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
        verify(clientDataService, never()).produceClientData(anyString(), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));
        verify(processStateService, never()).markAsCompleted(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si la ruta de salida es nula")
    void shouldFailWhenOutputPathIsNull() {
        String extractId = "ext-789";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = null; // Condición de falla
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), eq("La ruta de salida no puede ser nula o vacía."));
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
        verify(formatTrait, never()).getFormatByProductId(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si la ruta de salida existe pero no es un directorio")
    void shouldFailWhenOutputPathIsNotADirectory() throws Exception {
        String extractId = "ext-101";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path/es/un/archivo.txt";
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";

        pathsMock.when(() -> Paths.get(routeExitExtract)).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(false); // Condición de falla

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), anyString());
        verify(processStateService).markAsFailed(eq(extractId), eq("La ruta de salida '" + routeExitExtract + "' existe pero no es un directorio."));
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
        verify(formatTrait, never()).getFormatByProductId(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si no hay espacio suficiente en disco")
    void shouldFailWhenInsufficientDiskSpace() throws Exception {
        String extractId = "ext-202";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 10;
        String productName = "Producto Test";
        String token = "fake-token";

        int pdfSizeKb = 100;
        long availableSpace = 1024;

        when(performance.getPdfMaxSizeKb()).thenReturn(pdfSizeKb);

        pathsMock.when(() -> Paths.get(routeExitExtract)).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(availableSpace);

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), anyString());
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
        verify(formatTrait, never()).getFormatByProductId(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si la generación de HTML lanza una excepción")
    void shouldFailWhenHtmlGenerationThrowsException() throws Exception {
        String extractId = "ext-303";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 1;
        String productName = "Producto Test";
        String token = "fake-token";
        String exceptionMessage = "Error crítico de plantilla HTML";

        when(consumerPool.getMaxSize()).thenReturn(1);
        when(performance.getPdfWriteBatchSize()).thenReturn(5);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        pathsMock.when(() -> Paths.get(anyString(), anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        Map<String, Object> client1 = Map.of("name", "Cliente Uno", "fileName", "client1-file");

        doAnswer(invocation -> {
            BlockingQueue<Map<String, Object>> queue = invocation.getArgument(1);
            int consumerCount = invocation.getArgument(2);
            queue.put(client1);
            for (int i = 0; i < consumerCount; i++) {
                queue.put(ClientDataServiceImpl.END_OF_QUEUE);
            }
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        when(htmlTemplateService.prepareClientHtml(anyString(), anyMap(), any(FormatDto.class)))
                .thenThrow(new RuntimeException(exceptionMessage));

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), anyString());
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );

        verify(pdfGenerationService, never()).generatePdf(anyMap(), anyString());
        verify(processStateService, never()).markAsCompleted(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si el productor notifica un error")
    void shouldFailWhenProducerSetsFailureFlag() throws Exception {
        String extractId = "ext-404";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";
        String producerErrorMessage = "El proceso de generacion de extractos fallo. Revisar logs para mas detalles.";

        when(consumerPool.getMaxSize()).thenReturn(1);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        pathsMock.when(() -> Paths.get(anyString(), anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        doAnswer(invocation -> {
            AtomicBoolean isProcessFailed = invocation.getArgument(3);
            isProcessFailed.set(true);
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), eq(producerErrorMessage));
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );

        verify(htmlTemplateService, never()).prepareClientHtml(anyString(), anyMap(), any(FormatDto.class));
        verify(processStateService, never()).markAsCompleted(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica la falla del proceso si la generación de PDF lanza una excepción")
    void shouldFailWhenPdfGenerationThrowsException() throws Exception {
        String extractId = "ext-505";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 1;
        String productName = "Producto Test";
        String token = "fake-token";
        String exceptionMessage = "Error crítico de PDFBox";

        when(consumerPool.getMaxSize()).thenReturn(1);
        when(performance.getPdfWriteBatchSize()).thenReturn(5);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        pathsMock.when(() -> Paths.get(anyString(), anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        Map<String, Object> client1 = Map.of("name", "Cliente Uno", "fileName", "client1-file");

        doAnswer(invocation -> {
            BlockingQueue<Map<String, Object>> queue = invocation.getArgument(1);
            int consumerCount = invocation.getArgument(2);
            queue.put(client1);
            for (int i = 0; i < consumerCount; i++) {
                queue.put(ClientDataServiceImpl.END_OF_QUEUE);
            }
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        when(htmlTemplateService.prepareClientHtml(anyString(), anyMap(), any(FormatDto.class)))
                .thenReturn("<html>Cliente Uno</html>");

        when(pdfGenerationService.generatePdf(anyMap(), anyString()))
                .thenThrow(new RuntimeException(exceptionMessage));

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), anyString());
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );

        filesMock.verify(() -> Files.write(any(Path.class), any(byte[].class)), never());
        verify(processStateService, never()).markAsCompleted(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica que no se escriba en disco si los bytes del PDF son nulos o vacíos")
    void shouldSucceedButSkipPdfWriteWhenPdfBytesAreEmpty() throws Exception {
        String extractId = "ext-606";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";

        when(consumerPool.getMaxSize()).thenReturn(1);
        when(performance.getPdfWriteBatchSize()).thenReturn(5);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        pathsMock.when(() -> Paths.get(anyString(), anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        Map<String, Object> client1 = Map.of("name", "Cliente Uno", "fileName", "client1-file");
        Map<String, Object> client2 = Map.of("name", "Cliente Dos", "fileName", "client2-file");

        doAnswer(invocation -> {
            BlockingQueue<Map<String, Object>> queue = invocation.getArgument(1);
            int consumerCount = invocation.getArgument(2);
            queue.put(client1);
            queue.put(client2);
            for (int i = 0; i < consumerCount; i++) {
                queue.put(ClientDataServiceImpl.END_OF_QUEUE);
            }
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        when(htmlTemplateService.prepareClientHtml(anyString(), anyMap(), any(FormatDto.class)))
                .thenReturn("<html>Cliente Uno</html>")
                .thenReturn("<html>Cliente Dos</html>");

        when(pdfGenerationService.generatePdf(anyMap(), anyString()))
                .thenReturn(null) // Primera llamada retorna null
                .thenReturn(new byte[0]); // Segunda llamada retorna array vacío

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(pdfGenerationService, times(2)).generatePdf(anyMap(), anyString());

        filesMock.verify(() -> Files.write(any(Path.class), any(byte[].class)), never());

        verify(processStateService, times(2)).updateProgress(eq(extractId), anyLong(), eq((long) clientsProcessed));
        verify(processStateService).markAsCompleted(eq(extractId), anyString());
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.FINISH),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
    }

    @Test
    @DisplayName("Verifica la falla del proceso si la creación del directorio de salida falla")
    void shouldFailWhenDirectoryCreationFails() throws Exception {
        String extractId = "ext-707";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/new/fake/path";
        int clientsProcessed = 2;
        String productName = "Producto Test";
        String token = "fake-token";
        String exceptionMessage = "Permiso denegado";

        pathsMock.when(() -> Paths.get(routeExitExtract)).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(false); // No existe

        filesMock.when(() -> Files.createDirectories(mockPath))
                .thenThrow(new IOException(exceptionMessage)); // Falla al crear

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsFailed(eq(extractId), eq(exceptionMessage));
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.ERROR),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
        verify(formatTrait, never()).getFormatByProductId(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica que el proceso continúe si la escritura de un PDF falla")
    void shouldLogErrorsButContinueProcessingWhenFileWriteFails() throws Exception {
        String extractId = "ext-808";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 1;
        String productName = "Producto Test";
        String token = "fake-token";
        String exceptionMessage = "Disco protegido contra escritura";

        when(consumerPool.getMaxSize()).thenReturn(1);
        when(performance.getPdfWriteBatchSize()).thenReturn(5);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(anyString())).thenReturn(mockPath);
        pathsMock.when(() -> Paths.get(anyString(), anyString())).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        Map<String, Object> client1 = Map.of("name", "Cliente Uno", "fileName", "client1-file.pdf");

        doAnswer(invocation -> {
            BlockingQueue<Map<String, Object>> queue = invocation.getArgument(1);
            int consumerCount = invocation.getArgument(2);
            queue.put(client1);
            for (int i = 0; i < consumerCount; i++) {
                queue.put(ClientDataServiceImpl.END_OF_QUEUE);
            }
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        when(htmlTemplateService.prepareClientHtml(anyString(), anyMap(), any(FormatDto.class)))
                .thenReturn("<html>Cliente Uno</html>");

        byte[] pdfBytes1 = new byte[]{1, 2, 3};
        when(pdfGenerationService.generatePdf(anyMap(), anyString())).thenReturn(pdfBytes1);

        filesMock.when(() -> Files.write(mockPath, pdfBytes1))
                .thenThrow(new IOException(exceptionMessage));

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        verify(processStateService).markAsCompleted(eq(extractId), anyString());
        verify(emailNotificationService).sendProcessStatusNotification(
                eq(productName),
                eq(ExtractMessage.FINISH),
                anyString(),
                eq(ExtractMessage.SUBJECT_EXTRACTS)
        );
        verify(processStateService, never()).markAsFailed(anyString(), anyString());
    }

    @Test
    @DisplayName("Verifica que se use un nombre de archivo por defecto si no se provee")
    void shouldUseDefaultFilenameWhenNotProvided() throws Exception {
        String extractId = "ext-909";
        String productId = "prod-abc";
        String processId = "proc-xyz";
        String routeExitExtract = "/fake/path";
        int clientsProcessed = 1;
        String productName = "Producto Test";
        String token = "fake-token";

        Path mockDefaultPath = mock(Path.class);

        when(consumerPool.getMaxSize()).thenReturn(1);
        when(performance.getPdfWriteBatchSize()).thenReturn(5);
        when(performance.getPdfMaxSizeKb()).thenReturn(100);

        pathsMock.when(() -> Paths.get(routeExitExtract)).thenReturn(mockPath);
        filesMock.when(() -> Files.exists(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.isDirectory(mockPath)).thenReturn(true);
        filesMock.when(() -> Files.getFileStore(mockPath)).thenReturn(mockFileStore);
        when(mockFileStore.getUsableSpace()).thenReturn(Long.MAX_VALUE);

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(producerExecutor).execute(any(Runnable.class));

        doAnswer(invocation -> {
            Runnable task = invocation.getArgument(0);
            task.run();
            return null;
        }).when(consumerExecutor).execute(any(Runnable.class));

        FormatDto mockFormat = TestDataFactory.mockFormatDto();
        when(formatTrait.getFormatByProductId(productId, token)).thenReturn(mockFormat);

        Map<String, Object> client1 = Map.of("name", "Cliente Uno");

        doAnswer(invocation -> {
            BlockingQueue<Map<String, Object>> queue = invocation.getArgument(1);
            int consumerCount = invocation.getArgument(2);
            queue.put(client1);
            for (int i = 0; i < consumerCount; i++) {
                queue.put(ClientDataServiceImpl.END_OF_QUEUE);
            }
            return null;
        }).when(clientDataService).produceClientData(eq(processId), any(BlockingQueue.class), anyInt(), any(AtomicBoolean.class));

        when(htmlTemplateService.prepareClientHtml(anyString(), anyMap(), any(FormatDto.class)))
                .thenReturn("<html>Cliente Uno</html>");

        byte[] pdfBytes1 = new byte[]{1, 2, 3};
        when(pdfGenerationService.generatePdf(anyMap(), anyString())).thenReturn(pdfBytes1);

        pathsMock.when(() -> Paths.get(eq(routeExitExtract), anyString()))
                .thenAnswer(invocation -> {
                    String fileName = invocation.getArgument(1);
                    if (fileName.startsWith("extract-") && fileName.endsWith(".pdf")) {
                        return mockDefaultPath;
                    }
                    return mock(Path.class);
                });

        extractProcessorService.processExtractsAsync(extractId, productId, processId, routeExitExtract, clientsProcessed, productName, token);

        filesMock.verify(() -> Files.write(eq(mockDefaultPath), eq(pdfBytes1)), times(1));
        verify(processStateService).markAsCompleted(eq(extractId), anyString());
    }
}