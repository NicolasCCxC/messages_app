package avvillas.core.persistence.entity;

import avvillas.core.persistence.entity.id.ProcessDataId;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.Id;
import jakarta.persistence.IdClass;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Lob;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.PostLoad;
import jakarta.persistence.PostPersist;
import jakarta.persistence.Table;
import jakarta.persistence.Transient;
import jakarta.persistence.Index;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.data.domain.Persistable;

import java.io.Serializable;
import java.time.LocalDateTime;

@Entity
@Table(name = "data_procesada",indexes = {
        @Index(name = "idx_data_procesada_process_id", columnList = "process_id"),
})
@Data
@NoArgsConstructor
@AllArgsConstructor
@IdClass(ProcessDataId.class)
public class ProcessDataEntity implements Serializable, Persistable<ProcessDataId> {

    @Id
    @Column(name = "process_id")
    private String processId;

    @Id
    @Column(name = "client_id")
    private String clientId;

    @Column(name = "processing_timestamp", nullable = false)
    private LocalDateTime processingTimestamp = LocalDateTime.now();

    @Transient
    private boolean isNew = true;

    @Lob
    @Column(name = "data", nullable = false)
    private byte[] data;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "process_id", insertable = false, updatable = false)
    private LoadFilesEntryEntity loadProcess;

    public ProcessDataEntity(String processId, String clientId, byte[] data) {
        this.processId = processId;
        this.clientId = clientId;
        this.data = data;
        this.processingTimestamp = LocalDateTime.now();
        this.isNew = true;
    }

    @Override
    public ProcessDataId getId() {
        return new ProcessDataId(getProcessId(), getClientId());
    }

    @Override
    public boolean isNew() {
        return this.isNew;
    }

    @PostPersist
    @PostLoad
    void markNotNew() {
        this.isNew = false;
    }
}
