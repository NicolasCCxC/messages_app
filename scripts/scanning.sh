#!/bin/bash

# Detener el script si ocurre algún error
set -e

# Ir al directorio principal donde se encuentra el Dockerfile
cd "$(dirname "$0")/.."

# Verificando que exista el archivo para crear la imagen
if [ ! -f Dockerfile.scanning ]; then
  echo "Error: No se encontró el archivo Dockerfile en el directorio actual."
  exit 1
fi

# Verificar que existe pom.xml (validación adicional para proyectos Maven)
if [ ! -f pom.xml ]; then
  echo "Error: No se encontró el archivo pom.xml. Este script es para proyectos Maven."
  exit 1
fi

echo "Construyendo la imagen de Docker para Maven..."

if ! docker build -f Dockerfile.scanning \
    --build-arg MAVEN_VERSION="$MAVEN_VERSION" \
    --build-arg JAVA_VERSION="$JAVA_VERSION" \
    -t maven-build .; then
  echo "Error: La construcción de la imagen falló."
  exit 1
fi

# Verificar que la imagen realmente se haya creado
if ! docker image inspect maven-build >/dev/null 2>&1; then
  echo "Error: La imagen maven-build no se creó correctamente."
  exit 1
fi

echo "Imagen de Docker construida correctamente."
echo "Preparando la carpeta target..."

# Verificar si la carpeta target ya existe y eliminarla
if [ -d "./target" ]; then
  echo "La carpeta target ya existe. Eliminándola..."
  rm -rf ./target
fi

echo "Copiando los archivos compilados generados..."

# Crear el contenedor (sin ejecutarlo)
container_temporal=$(docker create maven-build)

# Validar que el contenedor temporal se creó correctamente
if [ -z "$container_temporal" ]; then
  echo "Error: No se pudo crear el contenedor temporal."
  exit 1
fi

# Copiar la carpeta target desde el contenedor al directorio local
docker cp "$container_temporal:/usr/src/app/target" ./target

# Eliminar el contenedor temporal
docker rm "$container_temporal" >/dev/null 2>&1

# Verificar que la compilación se copió correctamente
if [ -d "./target/classes" ]; then
  echo "✅ Compilación exitosa - Archivos .class generados:"
  ls -la ./target/classes
else
  echo "⚠️  Advertencia: No se encontraron archivos compilados en target/classes"
fi

echo "Proceso de compilación Maven finalizado correctamente."