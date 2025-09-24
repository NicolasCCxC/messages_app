package avvillas.core.persistence.entity;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.audit.AuditableEntity;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.EqualsAndHashCode;
import lombok.NoArgsConstructor;

import java.io.Serializable;

@Entity
@Table(name = "cargue_archivos_entrada")
@Data
@NoArgsConstructor
@AllArgsConstructor
@EqualsAndHashCode(callSuper = true)
public class LoadFilesEntryEntity extends AuditableEntity implements Serializable {

    @Id
    @GeneratedValue(strategy = GenerationType.UUID)
    @Column(name = "id", length = 36)
    private String id;

    @Enumerated(EnumType.STRING)
    @Column(name = "estado", length = 20)
    private LoadStatus status = LoadStatus.ACTIVO;

    @Column(name = "porcentaje_avance", precision = 3)
    private Integer percentAdvance = 1;

    @Column(name = "usuario_id", length = 36)
    private String userId;

    @Column(name = "periodo", length = 8)
    private String period;

    @Column(name = "detalles", length = 4000)
    private String details;

    @Column(name = "clientes_procesados")
    private Integer clientsProcessed;

    @Column(name = "producto_id", length = 36)
    private String productId;
}
