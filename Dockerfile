# Oracle linux 9 like SO
FROM oraclelinux:9-slim

# Maven and Java 21
RUN microdnf install dnf && \
    dnf -y update && \
    dnf -y install java-21-openjdk-devel maven && \
    dnf clean all

# ENV for java version
ENV JAVA_HOME=/usr/lib/jvm/java-21-openjdk
ENV PATH=$JAVA_HOME/bin:$PATH

# Create directory for save application
WORKDIR /app

# Copy project in directory created
COPY . /app

# Build project with maven
RUN mvn clean package -DskipTests

# Expose port where app executed
EXPOSE 8081

# Executed app generated with maven
CMD ["java", "-jar", "/app/target/core-1.0.jar"]