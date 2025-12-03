package avvillas.core.web.configuration;

import avvillas.core.service.implementations.extract_generation_impl.config.ExtractProperties;
import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.scheduling.annotation.EnableAsync;
import org.springframework.scheduling.concurrent.ThreadPoolTaskExecutor;

import java.util.concurrent.Executor;

@Configuration
@EnableAsync
@RequiredArgsConstructor
public class AsyncConfig {

    private static final Logger logger = LoggerFactory.getLogger(AsyncConfig.class);
    private final ExtractProperties properties;

    @Bean(name = "producerTaskExecutor")
    public Executor producerTaskExecutor() {
        ThreadPoolTaskExecutor executor = new ThreadPoolTaskExecutor();

        ExtractProperties.ProducerPool poolProps = properties.getPerformance().getProducerPool();

        executor.setCorePoolSize(poolProps.getCoreSize());
        executor.setMaxPoolSize(poolProps.getMaxSize());
        executor.setQueueCapacity(poolProps.getQueueCapacity());
        executor.setThreadNamePrefix(poolProps.getNamePrefix());


        logger.info(
                "[CONF] - Configurando ProducerTaskExecutor: CoreSize={}, MaxSize={}, QueueCapacity={}, Name={}",
                poolProps.getCoreSize(),
                poolProps.getMaxSize(),
                poolProps.getQueueCapacity(),
                poolProps.getNamePrefix()
        );

        executor.initialize();
        return executor;
    }

    @Bean(name = "consumerTaskExecutor")
    public Executor consumerTaskExecutor() {
        ThreadPoolTaskExecutor executor = new ThreadPoolTaskExecutor();

        ExtractProperties.ConsumerPool poolProps = properties.getPerformance().getConsumerPool();

        executor.setCorePoolSize(poolProps.getCoreSize());
        executor.setMaxPoolSize(poolProps.getMaxSize());
        executor.setQueueCapacity(poolProps.getQueueCapacity());
        executor.setThreadNamePrefix(poolProps.getNamePrefix());

        logger.info(
                "[CONF] - Configurando ConsumerTaskExecutor: CoreSize={}, MaxSize={}, QueueCapacity={}, Name={}",
                poolProps.getCoreSize(),
                poolProps.getMaxSize(),
                poolProps.getQueueCapacity(),
                poolProps.getNamePrefix()
        );

        executor.initialize();
        return executor;
    }
}