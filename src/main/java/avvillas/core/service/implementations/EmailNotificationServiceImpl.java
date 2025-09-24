package avvillas.core.service.implementations;

import avvillas.core.service.EmailNotificationService;
import jakarta.mail.MessagingException;
import jakarta.mail.internet.MimeMessage;
import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.mail.MailException;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.mail.javamail.MimeMessageHelper;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Service;

@Service
@RequiredArgsConstructor
public class EmailNotificationServiceImpl implements EmailNotificationService {

    private static final Logger logger = LoggerFactory.getLogger(EmailNotificationServiceImpl.class);

    private final JavaMailSender mailSender;

    @Value("${spring.mail.username}")
    private String mailFrom;

    @Value("${notifications.mail.recipients}")
    private String[] mailRecipients;

    @Override
    @Async
    public void sendProcessStatusNotification(String productName, String status, String details, String subject) {
        if (mailRecipients == null || mailRecipients.length == 0) {
            logger.error("No hay destinatarios de correo configurados. No se enviará la notificación para la generación de índices del producto {}.", productName);
            return;
        }

        try {
            MimeMessage message = mailSender.createMimeMessage();
            MimeMessageHelper helper = new MimeMessageHelper(message, true, "UTF-8");

            helper.setFrom(mailFrom);
            helper.setTo(mailRecipients);
            helper.setSubject(String.format("[%s] Producto %s - %s", subject, productName, status));
            String htmlBody = buildHtmlEmailBody(productName, status, details, subject);
            helper.setText(htmlBody, true);

            mailSender.send(message);
            logger.info("Notificación por correo enviada para {} del producto {} con estado {}", subject, productName, status);

        } catch (MailException | MessagingException e) {
            logger.error("Error al enviar el correo de notificación para el producto {}: {}", productName, e.getMessage());
        }
    }

    private String buildHtmlEmailBody(String productName, String status, String details, String subject) {
        String formattedDetails = details.replace("\n", "<br>");

        return "<html>"
                + "<body style='font-family: Arial, sans-serif;'>"
                + "<h2>Notificación de Proceso de " + subject + " </h2>"
                + "<p>Se ha actualizado el estado de " + subject + ".</p>"
                + "<hr>"
                + "<p><strong>Producto:</strong> " + productName + "</p>"
                + "<p><strong>Estado Final:</strong> <strong style='color:" + getStatusColor(status) + ";'>" + status + "</strong></p>"
                + "<h3>Detalles:</h3>"
                + "<pre style='background-color: #f4f4f4; padding: 10px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word;'>"
                + formattedDetails
                + "</pre>"
                + "<hr>"
                + "<p><small>Este es un correo generado automáticamente.</small></p>"
                + "</body>"
                + "</html>";
    }

    private String getStatusColor(String status) {
        return switch (status) {
            case "FINALIZADO" -> "green";
            case "ERROR" -> "red";
            default -> "black";
        };
    }
}
