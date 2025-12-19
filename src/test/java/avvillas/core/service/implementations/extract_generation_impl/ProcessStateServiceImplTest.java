package avvillas.core.service.implementations.extract_generation_impl;

import avvillas.core.common.BaseServiceTest;
import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.repository.ExtractRepository;
import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyInt;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.never;
import static org.mockito.Mockito.times;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@MockitoSettings(strictness = Strictness.LENIENT)
class ProcessStateServiceImplTest extends BaseServiceTest {

    private static final String EXTRACT_ID = "test-extract-id";
    private static final int THRESHOLD_PERCENT = 10;
    private static final long THRESHOLD_MS = 1000L;

    @Mock
    private ExtractRepository extractRepository;
    @Mock
    private ExtractProperties extractProperties;
    @Mock
    private ExtractProperties.State stateProperties;

    @InjectMocks
    private ProcessStateServiceImpl processStateService;

    @BeforeEach
    void setUp() {
        when(extractProperties.getState()).thenReturn(stateProperties);
        when(stateProperties.getProgressThresholdPercent()).thenReturn(THRESHOLD_PERCENT);
        when(stateProperties.getTimeThresholdMs()).thenReturn(THRESHOLD_MS);

        when(extractRepository.updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any()))
                .thenReturn(1);
    }

    @Test
    @DisplayName("Verifica que updateProgress ignore un extractId no registrado")
    void updateProgress_shouldIgnoreUnregisteredExtractId() {
        processStateService.updateProgress("unregistered-id", 50, 100);
        verify(extractRepository, never()).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
    }

    @Test
    @DisplayName("Verifica que updateProgress ignore si totalItems es cero")
    void updateProgress_shouldIgnoreWhenTotalItemsIsZero() {
        processStateService.registerProcess(EXTRACT_ID);
        processStateService.updateProgress(EXTRACT_ID, 50, 0);
        verify(extractRepository, never()).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
    }

    @Test
    @DisplayName("Verifica que updateProgress ignore si el umbral de porcentaje no se cumple (después de un guardado)")
    void updateProgress_shouldThrottleByPercentThreshold() {
        processStateService.registerProcess(EXTRACT_ID);

        processStateService.updateProgress(EXTRACT_ID, 1, 100);

        processStateService.updateProgress(EXTRACT_ID, 5, 100);

        verify(extractRepository, times(1)).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
        verify(extractRepository, times(1)).updateProgressDirectly(eq(EXTRACT_ID), eq(1), eq(LoadStatus.ACTIVO), any());
    }

    @Test
    @DisplayName("Verifica que updateProgress guarde si el umbral de porcentaje se cumple")
    void updateProgress_shouldPersistWhenPercentThresholdIsMet() {
        processStateService.registerProcess(EXTRACT_ID);

        processStateService.updateProgress(EXTRACT_ID, 10, 100);

        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(10), eq(LoadStatus.ACTIVO), any());
    }

    @Test
    @DisplayName("Verifica que updateProgress ignore actualizaciones después de un guardado (throttling)")
    void updateProgress_shouldThrottleAfterRecentPersist() {
        processStateService.registerProcess(EXTRACT_ID);

        processStateService.updateProgress(EXTRACT_ID, 10, 100);

        processStateService.updateProgress(EXTRACT_ID, 12, 100);

        verify(extractRepository, times(1)).updateProgressDirectly(eq(EXTRACT_ID), eq(10), eq(LoadStatus.ACTIVO), any());
    }

    @Test
    @DisplayName("Verifica que updateProgress guarde si el umbral de tiempo se cumple")
    @SuppressWarnings("java:S2925")
    void updateProgress_shouldPersistWhenTimeThresholdIsMet() throws InterruptedException {
        when(stateProperties.getTimeThresholdMs()).thenReturn(50L);
        processStateService.registerProcess(EXTRACT_ID);

        processStateService.updateProgress(EXTRACT_ID, 2, 100);

        processStateService.updateProgress(EXTRACT_ID, 3, 100);

        Thread.sleep(60);

        processStateService.updateProgress(EXTRACT_ID, 4, 100);

        verify(extractRepository, times(2)).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(2), eq(LoadStatus.ACTIVO), any());
        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(4), eq(LoadStatus.ACTIVO), any());
    }

    @Test
    @DisplayName("Verifica que markAsCompleted guarde el estado final y limpie el caché")
    void markAsCompleted_shouldPersistFinalStateAndClearCache() {
        processStateService.registerProcess(EXTRACT_ID);
        String message = "Proceso completado";

        processStateService.markAsCompleted(EXTRACT_ID, message);

        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(100), eq(LoadStatus.FINALIZADO), eq(message));

        processStateService.updateProgress(EXTRACT_ID, 1, 100);
        verify(extractRepository, times(1)).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
    }

    @Test
    @DisplayName("Verifica que markAsFailed guarde el estado de error y limpie el caché")
    void markAsFailed_shouldPersistErrorStateAndClearCache() {
        processStateService.registerProcess(EXTRACT_ID);
        String message = "Proceso fallido";

        processStateService.markAsFailed(EXTRACT_ID, message);

        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(-1), eq(LoadStatus.ERROR), eq(message));

        processStateService.updateProgress(EXTRACT_ID, 1, 100);
        verify(extractRepository, times(1)).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
    }

    @Test
    @DisplayName("Verifica que persistProgress trunque los mensajes largos")
    void persistProgress_shouldTruncateLongDetails() {
        String longMessage = "A".repeat(5000);
        String truncatedMessage = "A".repeat(4000);

        ArgumentCaptor<String> detailsCaptor = ArgumentCaptor.forClass(String.class);

        processStateService.markAsFailed(EXTRACT_ID, longMessage);

        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(-1), eq(LoadStatus.ERROR), detailsCaptor.capture());
        assertEquals(4000, detailsCaptor.getValue().length());
        assertEquals(truncatedMessage, detailsCaptor.getValue());
    }

    @Test
    @DisplayName("Verifica que una falla de BD sea capturada y se limpie el caché")
    void persistProgress_shouldCatchExceptionAndStillClearCache() {
        processStateService.registerProcess(EXTRACT_ID);
        String message = "Proceso fallido";

        when(extractRepository.updateProgressDirectly(eq(EXTRACT_ID), anyInt(), any(LoadStatus.class), any()))
                .thenThrow(new RuntimeException("Error de BD"));

        processStateService.markAsFailed(EXTRACT_ID, message);

        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(-1), eq(LoadStatus.ERROR), eq(message));

        processStateService.updateProgress(EXTRACT_ID, 1, 100);
        verify(extractRepository, times(1)).updateProgressDirectly(anyString(), anyInt(), any(LoadStatus.class), any());
    }

    @Test
    @DisplayName("Verifica que persistProgress no falle si el repositorio reporta 0 actualizaciones")
    void persistProgress_shouldHandleZeroUpdatesGracefully() {
        when(extractRepository.updateProgressDirectly(eq(EXTRACT_ID), anyInt(), any(LoadStatus.class), any()))
                .thenReturn(0);

        processStateService.markAsCompleted(EXTRACT_ID, "Completado");

        verify(extractRepository).updateProgressDirectly(eq(EXTRACT_ID), eq(100), eq(LoadStatus.FINALIZADO), any());
    }
}