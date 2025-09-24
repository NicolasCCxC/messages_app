package avvillas.core.persistence.entity;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.audit.AuditableEntity;

import jakarta.persistence.Table;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.Index;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.EqualsAndHashCode;
import lombok.NoArgsConstructor;

import java.io.Serializable;

@Entity
@Table(name = "archivo_indices", indexes = {
        @Index(name = "idx_archivo_indices_producto_id", columnList = "producto_id"),
        @Index(name = "idx_archivo_indices_periodo", columnList = "periodo"),
        @Index(name = "idx_archivo_indices_estado", columnList = "estado"),
})
@Data
@NoArgsConstructor
@AllArgsConstructor
@EqualsAndHashCode(callSuper = true)
public class IndexFileEntity extends AuditableEntity implements Serializable {

    @Id
    @GeneratedValue(strategy = GenerationType.UUID)
    @Column(name = "id", length = 36)
    private String id;

    @Column(name = "process_id", length = 36)
    private String processId;

    @Column(name = "producto_id", length = 36, nullable = false)
    private String productId;

    @Column(name = "periodo", length = 8)
    private String period;

    @Column(name = "usuario", length = 100)
    private String user;

    @Enumerated(EnumType.STRING)
    @Column(name = "estado", length = 20)
    private LoadStatus status = LoadStatus.ACTIVO;

    @Column(name = "ruta_salida_extracto", length = 255)
    private String route;

    @Column(name = "clientes_procesados")
    private Integer clientsProcessed;

    @Column(name = "avance")
    private int percentAdvance;
}