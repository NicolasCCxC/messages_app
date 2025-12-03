package avvillas.core.service.implementations.extract_generation_impl.config;

import jakarta.validation.constraints.Min;
import lombok.Data;
import org.springframework.boot.context.properties.ConfigurationProperties;
import org.springframework.context.annotation.Configuration;
import org.springframework.validation.annotation.Validated;


@Data
@Validated
@Configuration
@ConfigurationProperties(prefix = "extract")
public class ExtractProperties {

    private Process process = new Process();
    private Performance performance = new Performance();
    private State state = new State();

    @Data
    public static class Process {
        private String defaultOutputPath = "extracts_output";
        private String barcodeWidth = "350";
        private String barcodeHeight = "50";
    }

    @Data
    public static class Performance {
        @Min(100)
        private int queueCapacity = 2000;

        @Min(10)
        private int pdfWriteBatchSize = 50;

        @Min(1)
        private int pdfMaxSizeKb = 96;

        private ProducerPool producerPool = new ProducerPool();
        private ConsumerPool consumerPool = new ConsumerPool();
    }


    @Data
    public static class ProducerPool {
        private String namePrefix = "ExtractProducer-";
        @Min(1)
        private int coreSize = 2;
        @Min(1)
        private int maxSize = 4;
        @Min(50)
        private int queueCapacity = 100;
        @Min(100)
        private int idBatchSize = 1000;
    }

    @Data
    public static class ConsumerPool {
        private String namePrefix = "ExtractConsumer-";
        @Min(1)
        private int coreSize = 4;
        @Min(1)
        private int maxSize = 8;
        @Min(50)
        private int queueCapacity = 100;
    }

    @Data
    public static class State {
        @Min(1)
        private int progressThresholdPercent = 10;
        @Min(1000)
        private long timeThresholdMs = 10000;
    }
}
