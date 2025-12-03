package avvillas.core.persistence.repository;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.LoadFilesEntryEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.Optional;

@Repository
public interface LoadFilesEntryRepository extends JpaRepository<LoadFilesEntryEntity, String>,
        JpaSpecificationExecutor<LoadFilesEntryEntity> {

    Optional<LoadFilesEntryEntity> findByProductIdAndStatus(String productId, LoadStatus status);

    LoadFilesEntryEntity findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(String productId, String period, LoadStatus status);

    @Modifying
    @Transactional
    @Query("UPDATE LoadFilesEntryEntity p SET p.status = :status, p.details = :details, p.percentAdvance = :percent, p.updateAt = :now ,p.clientsProcessed = :clientsProcessed WHERE p.id = :id")
    int updateProcessStatus(
            @Param("id") String id,
            @Param("status") LoadStatus status,
            @Param("details") String details,
            @Param("percent") Integer percent,
            @Param("now") LocalDateTime now,
            @Param("clientsProcessed") Long clientsProcessed
    );
}
