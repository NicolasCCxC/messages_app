package avvillas.core.constant.message;

public class ExtractMessage {

    private ExtractMessage() {}

    public static final String EXTRACT_PROCESS = "Proceso de generación de extractos activado";
    public static final String EXTRACT_FINISH = "Proceso de generación de extractos finalizado con exito";
    public static final String EXTRACT_ERROR = "Error durante el proceso de generación de extractos: %s";
    public static final String EXTRACT_ERROR_NOT_DATA = "Producto sin información para procesar";

    public static final String ERROR_EXTRACT_PROCESS = "Ya existe un proceso con estado activo para el producto con el id: %s";
    public static final String SUBJECT_EXTRACTS = "Generación de Extractos";

    public static final String FINISH = "FINALIZADO";
    public static final String ERROR = "ERROR";
}