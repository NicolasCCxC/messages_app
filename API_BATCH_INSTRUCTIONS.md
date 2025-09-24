# Instrucciones para ejecutar la API de Extractos desde archivos de script

Este documento explica cómo utilizar los scripts proporcionados para llamar a la API de generación de extractos:
- `call_extract_api.bat`: Script de archivo batch tradicional
- `call_extract_api.ps1`: Script de PowerShell (opción alternativa con características avanzadas)

## Requisitos previos

1. Windows OS
2. Curl instalado (viene preinstalado en Windows 10 y versiones posteriores)
3. El servidor de la aplicación debe estar en ejecución en el puerto 8087

## Configuración del archivo batch

Antes de ejecutar el archivo batch, necesitas configurar algunas variables:

1. Abre el archivo `call_extract_api.bat` en un editor de texto
2. Modifica la variable `JSON_DATA` según tus necesidades:
   - `productId`: Reemplaza con el UUID del producto (formato UUID v4)
   - `period`: Reemplaza con el período en formato YYYYMMDD (8 caracteres)

Ejemplo:
```batch
SET JSON_DATA={"productId":"123e4567-e89b-42d3-a456-556642440000","period":"20250804"}
```

## Ejecución del archivo batch

1. Haz doble clic en el archivo `call_extract_api.bat` desde el Explorador de Windows
   - O abre una ventana de Command Prompt y ejecuta: `call_extract_api.bat`

2. El archivo batch mostrará:
   - La URL que está llamando
   - El cuerpo de la solicitud (JSON)
   - La respuesta de la API
   - Un mensaje "API call completed" cuando termine

3. Presiona cualquier tecla para cerrar la ventana cuando hayas terminado de revisar los resultados

## Respuesta de la API

La respuesta de la API será un objeto JSON con el siguiente formato:

```json
{
  "data": {
    "productId": "123e4567-e89b-42d3-a456-556642440000",
    "period": "20250804",
    "state": "activo",
    "progress": "0",
    "userId": "user-uuid-here"
  },
  "message": ["Proceso de extractos completado"],
  "service": "EXTRACT_SERVICE",
  "log_entries": null
}
```

Los archivos PDF generados se guardarán en la carpeta configurada en `extractos.folder.path` en el archivo `application.properties`.

## Solución de problemas

Si encuentras errores al ejecutar el archivo batch:

1. Verifica que el servidor de la aplicación esté en ejecución
2. Comprueba que el productId tenga un formato UUID válido
3. Comprueba que el period tenga exactamente 8 caracteres en formato YYYYMMDD
4. Asegúrate de que el JSON_DATA tenga un formato válido (sin saltos de línea)
5. Verifica que curl esté instalado ejecutando `curl --version` en Command Prompt

## Uso del script de PowerShell (Alternativa)

Como alternativa al archivo batch, también se proporciona un script de PowerShell (`call_extract_api.ps1`) que ofrece características más avanzadas.

### Requisitos para PowerShell

1. Windows OS con PowerShell 3.0 o superior (preinstalado en Windows 7 SP1 y versiones posteriores)
2. Permisos para ejecutar scripts de PowerShell (puede requerir cambiar la política de ejecución)

### Configuración del script de PowerShell

1. Abre el archivo `call_extract_api.ps1` en un editor de texto o en PowerShell ISE
2. Modifica el objeto JSON según tus necesidades:

Ejemplo:
```powershell
$jsonData = @{
    productId = "123e4567-e89b-42d3-a456-556642440000"
    period = "20250804"
} | ConvertTo-Json -Compress
```

### Ejecución del script de PowerShell

1. Abre PowerShell como administrador
2. Navega al directorio donde se encuentra el script:
   ```powershell
   cd "ruta\al\directorio"
   ```
3. Si es la primera vez que ejecutas scripts de PowerShell, puede que necesites cambiar la política de ejecución:
   ```powershell
   Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
   ```
4. Ejecuta el script:
   ```powershell
   .\call_extract_api.ps1
   ```

### Ventajas del script de PowerShell

- Mejor manejo de JSON: Utiliza las capacidades nativas de PowerShell para trabajar con JSON
- Manejo de errores más robusto: Incluye un bloque try-catch para manejar errores de forma elegante
- Mejor análisis de la respuesta: Puede acceder directamente a las propiedades de la respuesta JSON
- Formato de salida mejorado: Muestra los archivos generados en un formato más legible