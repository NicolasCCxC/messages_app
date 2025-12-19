package avvillas.core.constant;

import avvillas.core.constant.message.EntryMessage;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;

import static org.assertj.core.api.Assertions.assertThat;

class EntryMessageTest {

    @Test
    @DisplayName("Verifica que las constantes de mensajes no sean nulas")
    void constantsShouldNotBeNull() {
        assertThat(EntryMessage.PATH_EXTRACTS_ARCHIVE_INDEX_NOT_FOUND_BY_PRODUCT_ID).isNotNull();
        assertThat(EntryMessage.PRODUCT_NOT_FOUND).isNotNull();
        assertThat(EntryMessage.CONTENT_NOT_FOUND_BY_PRODUCT_ID).isNotNull();
        assertThat(EntryMessage.ERROR_GET_FORMAT).isNotNull();
    }

    @Test
    @DisplayName("Verifica la invocación del constructor privado para cobertura")
    void shouldCoverPrivateConstructor() throws NoSuchMethodException, InstantiationException, IllegalAccessException, InvocationTargetException {
        Constructor<EntryMessage> constructor = EntryMessage.class.getDeclaredConstructor();
        constructor.setAccessible(true);
        EntryMessage instance = constructor.newInstance();

        assertThat(instance).isNotNull();
    }
}