package avvillas.core.persistence.entity;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.audit.AuditableEntity;

import jakarta.persistence.Table;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.EnumType;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.EqualsAndHashCode;
import lombok.NoArgsConstructor;

import java.io.Serializable;

@Entity
@Table(name = "extractos")
@Data
@NoArgsConstructor
@AllArgsConstructor
@EqualsAndHashCode(callSuper = true)
public class ExtractEntity extends AuditableEntity implements Serializable {

    @Id
    @GeneratedValue(strategy = GenerationType.UUID)
    @Column(name = "id", length = 36)
    private String id;

    @Column(name = "producto_id", length = 36, nullable = false)
    private String productId;

    @Column(name = "usuario_id", length = 36)
    private String user;

    @Column(name = "periodo", length = 8)
    private String period;

    @Enumerated(EnumType.STRING)
    @Column(name = "estado", length = 20)
    private LoadStatus status = LoadStatus.ACTIVO;

    @Column(name = "avance")
    private int percentAdvance;

    @Column(name = "detalles", length = 4000)
    private String details;
}