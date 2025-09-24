CREATE TABLE cargue_archivos_entrada (
    id VARCHAR2(36) NOT NULL,
    estado VARCHAR2(20) NOT NULL,
    porcentaje_avance NUMBER(3),
    usuario_id VARCHAR2(36),
    periodo VARCHAR2(8),
    detalles VARCHAR2(4000),
    clientes_procesados NUMBER,
    producto_id VARCHAR2(36),
    CONSTRAINT pk_cargue_archivos_entrada PRIMARY KEY (id)
);

-- Tabla de datos del cliente, optimizada según el documento de arquitectura
CREATE TABLE data_procesada (
    process_id VARCHAR2(36) NOT NULL,
    client_id VARCHAR2(255) NOT NULL,
    processing_timestamp TIMESTAMP(6) DEFAULT SYSTIMESTAMP NOT NULL,
    data BLOB,
    CONSTRAINT pk_processdata PRIMARY KEY (process_id, client_id),
    CONSTRAINT fk_processdata_cargue FOREIGN KEY (process_id) REFERENCES cargue_archivos_entrada(id),
    CONSTRAINT chk_processdata_json CHECK (data IS JSON)
)
LOB (data) STORE AS SECUREFILE (
  DISABLE STORAGE IN ROW
  CHUNK 32768
  NOCACHE
  --COMPRESS MEDIUM        TODO: Hacer benchmarlk de rendimiento con el fin de comprobar si es nesceario o no
)
PARTITION BY RANGE (processing_timestamp)
INTERVAL (NUMTODSINTERVAL(1, 'DAY'))
(
  PARTITION p_initial VALUES LESS THAN (TO_TIMESTAMP('2024-01-01', 'YYYY-MM-DD'))
);