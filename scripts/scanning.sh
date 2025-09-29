#!/bin/bash

# Detener el script si ocurre algún error
set -e

# Ir al directorio principal donde se encuentra el package.json
cd "$(dirname "$0")/.."

# Verificar que existe package.json
if [ ! -f package.json ]; then
  echo "Error: No se encontró el archivo package.json. Este script es para proyectos Node.js."
  exit 1
fi

echo "Ejecutando tests y generando cobertura en Docker..."

# Verificar que NODE_VERSION está definido
if [ -z "$NODE_VERSION" ]; then
  echo "Error: NODE_VERSION no está definida. Por favor, establecer la versión de Node.js."
  exit 1
fi

# Usar la versión de node especificada
docker run --rm \
  -v "$(pwd):/app" \
  -w /app \
  node:${NODE_VERSION}-alpine \
  sh -c "npm install && npm run test:coverage"

# Verificar que los archivos de cobertura se generaron correctamente
if [ -d "./coverage" ]; then
  echo "Tests completados exitosamente:"
  echo "- Carpeta coverage: $(ls -la ./coverage | wc -l) archivos"
else
  echo "Advertencia: No se encontraron archivos de cobertura"
  exit 1
fi

echo "Proceso de testing finalizado correctamente."