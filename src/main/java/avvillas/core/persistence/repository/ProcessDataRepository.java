package avvillas.core.persistence.repository;

import avvillas.core.persistence.entity.ProcessDataEntity;
import avvillas.core.persistence.entity.id.ProcessDataId;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface ProcessDataRepository extends JpaRepository<ProcessDataEntity, ProcessDataId> {

    Optional<List<ProcessDataEntity>> findByProcessId(String id);

    @Query("select pde.id.clientId from ProcessDataEntity pde where pde.id.processId = :processId")
    Page<String> findClientIdsByProcessId(String processId, Pageable pageable);
}