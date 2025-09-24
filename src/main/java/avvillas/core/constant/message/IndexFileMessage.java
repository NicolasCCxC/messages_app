package avvillas.core.constant.message;

public class IndexFileMessage {

    private IndexFileMessage() {}

    public static final String INDEX_FILE_NOT_FOUND = "Archivo de indice no encontrado con id: %s";

    public static final String INDEX_FILE_NOT_FOUND_BY_PRODUCT_ID = "No hay un archivo de indice parametrizado para el producto con el id: %s y el perido: %s";

    public static final String PRODUCT_ID_REQUIRED = "El campo productId es obligatorio";
    public static final String PRODUCT_ID_INVALID = "El campo productId tiene un formato UUID no válido";

    public static final String PERIOD_REQUIRED = "El campo period es obligatorio";
    public static final String PERIOD_MAX = "El campo 'period' debe tener como máximo 8 caracteres";

    public static final String ERROR_INDEX_FILE_PROCESS = "Ya existe un proceso con estado activo para el producto con el id: %s";
    public static final String ERROR_INDEX_FILE_TOTAL_DATA = "No coincide la cantidad de índices generados: %s; contra la cantidad de extractos a generar: %s";

    public static final String PROCESS_INDEX_FINALIZED = "Proceso de generación de archivos de índices finalizado para el producto: %s. Total de índices generados: %s; total de extractos a generar: %s.";

    public static final String INDEX_FILE_PROCESS = "Proceso de generación de índices activado";

}