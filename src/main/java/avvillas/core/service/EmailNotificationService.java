package avvillas.core.service;

import org.springframework.scheduling.annotation.Async;

public interface EmailNotificationService {
    @Async
    void sendProcessStatusNotification(String productName, String status, String details, String subject);
}
