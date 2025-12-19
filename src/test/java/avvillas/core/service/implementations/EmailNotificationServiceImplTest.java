package avvillas.core.service.implementations;

import avvillas.core.common.BaseServiceTest;
import jakarta.mail.BodyPart;
import jakarta.mail.Session;
import jakarta.mail.internet.MimeMessage;
import jakarta.mail.internet.MimeMultipart;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;
import org.springframework.mail.MailSendException;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.test.util.ReflectionTestUtils;

import java.util.Properties;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.Mockito.doThrow;
import static org.mockito.Mockito.never;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@MockitoSettings(strictness = Strictness.LENIENT)
class EmailNotificationServiceImplTest extends BaseServiceTest {

    @Mock
    private JavaMailSender mailSender;

    @InjectMocks
    private EmailNotificationServiceImpl emailService;

    private MimeMessage mimeMessage;

    @BeforeEach
    void setUp() {
        Session session = Session.getDefaultInstance(new Properties());
        mimeMessage = new MimeMessage(session);

        ReflectionTestUtils.setField(emailService, "mailFrom", "pruebas@avvillas.com");
        ReflectionTestUtils.setField(emailService, "mailRecipients", new String[]{"destinatario1@test.com"});

        when(mailSender.createMimeMessage()).thenReturn(mimeMessage);
    }

    private String getHtmlContent(MimeMessage message) throws Exception {
        message.saveChanges();

        Object content = message.getContent();

        if (content instanceof MimeMultipart) {
            MimeMultipart multipart = (MimeMultipart) content;
            return extractHtmlFromMultipart(multipart);
        } else if (content instanceof String) {
            return (String) content;
        }

        return content != null ? content.toString() : "";
    }

    private String extractHtmlFromMultipart(MimeMultipart multipart) throws Exception {
        for (int i = 0; i < multipart.getCount(); i++) {
            BodyPart bodyPart = multipart.getBodyPart(i);

            if (bodyPart.isMimeType("text/html")) {
                Object partContent = bodyPart.getContent();
                if (partContent instanceof String) {
                    return (String) partContent;
                }
            } else if (bodyPart.isMimeType("text/plain")) {
                Object partContent = bodyPart.getContent();
                if (partContent instanceof String) {
                    return (String) partContent;
                }
            } else if (bodyPart.getContent() instanceof MimeMultipart) {
                String result = extractHtmlFromMultipart((MimeMultipart) bodyPart.getContent());
                if (result != null && !result.isEmpty()) {
                    return result;
                }
            }
        }

        return "";
    }

    @Test
    @DisplayName("Verifica que no se envíe correo si los destinatarios son nulos")
    void shouldNotSendEmailWhenRecipientsAreNull() {
        ReflectionTestUtils.setField(emailService, "mailRecipients", null);
        emailService.sendProcessStatusNotification("ProductoA", "ESTADO", "Detalles", "Asunto");
        verify(mailSender, never()).createMimeMessage();
        verify(mailSender, never()).send(mimeMessage);
    }

    @Test
    @DisplayName("Verifica que no se envíe correo si la lista de destinatarios está vacía")
    void shouldNotSendEmailWhenRecipientsAreEmpty() {
        ReflectionTestUtils.setField(emailService, "mailRecipients", new String[0]);
        emailService.sendProcessStatusNotification("ProductoA", "ESTADO", "Detalles", "Asunto");
        verify(mailSender, never()).createMimeMessage();
        verify(mailSender, never()).send(mimeMessage);
    }

    @Test
    @DisplayName("Verifica el envío exitoso de correo con estado FINALIZADO")
    void shouldSendEmailSuccessfullyForFinalizadoStatus() throws Exception {
        String productName = "Producto Préstamos";
        String status = "FINALIZADO";
        String details = "El proceso terminó.\nTodo OK.";
        String subject = "Generación de Índices";

        emailService.sendProcessStatusNotification(productName, status, details, subject);

        ArgumentCaptor<MimeMessage> messageCaptor = ArgumentCaptor.forClass(MimeMessage.class);
        verify(mailSender).send(messageCaptor.capture());

        MimeMessage sentMessage = messageCaptor.getValue();
        String htmlContent = getHtmlContent(sentMessage);

        assertThat(sentMessage.getSubject()).isEqualTo("[Generación de Índices] Producto Producto Préstamos - FINALIZADO");
        assertThat(htmlContent)
                .contains("Producto Préstamos")
                .contains("color:green")
                .contains("El proceso terminó.<br>Todo OK.");
    }

    @Test
    @DisplayName("Verifica el envío exitoso de correo con estado ERROR")
    void shouldSendEmailSuccessfullyForErrorStatus() throws Exception {
        String productName = "Producto Tarjetas";
        String status = "ERROR";
        String details = "Falló la conexión.";
        String subject = "Generación de Extractos";

        emailService.sendProcessStatusNotification(productName, status, details, subject);

        ArgumentCaptor<MimeMessage> messageCaptor = ArgumentCaptor.forClass(MimeMessage.class);
        verify(mailSender).send(messageCaptor.capture());

        MimeMessage sentMessage = messageCaptor.getValue();
        String htmlContent = getHtmlContent(sentMessage);

        assertThat(sentMessage.getSubject()).isEqualTo("[Generación de Extractos] Producto Producto Tarjetas - ERROR");
        assertThat(htmlContent)
                .contains("Producto Tarjetas")
                .contains("color:red")
                .contains("Falló la conexión.");
    }

    @Test
    @DisplayName("Verifica que las excepciones MailException sean capturadas")
    void shouldCatchMailExceptionOnSend() {
        doThrow(new MailSendException("Error simulado de envío"))
                .when(mailSender).send(mimeMessage);

        emailService.sendProcessStatusNotification("ProductoA", "ESTADO", "Detalles", "Asunto");

        verify(mailSender).send(mimeMessage);
    }
}