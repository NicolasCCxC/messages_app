package avvillas.core.web.configuration.deserializer;

import avvillas.core.constant.message.CommonMessage;
import com.fasterxml.jackson.core.JsonParser;
import com.fasterxml.jackson.core.JsonToken;
import com.fasterxml.jackson.databind.DeserializationContext;
import com.fasterxml.jackson.databind.JsonDeserializer;
import com.fasterxml.jackson.databind.exc.InvalidFormatException;

import java.io.IOException;

public class StrictBooleanDeserializer extends JsonDeserializer<Boolean> {
    @Override
    public Boolean deserialize(JsonParser parser, DeserializationContext ctx) throws IOException {
        JsonToken token = parser.getCurrentToken();
        if (token == JsonToken.VALUE_TRUE) {
            return true;
        } else if (token == JsonToken.VALUE_FALSE) {
            return false;
        }
        throw new InvalidFormatException(parser, CommonMessage.BOOLEAN_REQUIRED, parser.getText(), Boolean.class);
    }
}
