package avvillas.core.persistence.repository;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.ExtractEntity;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface ExtractRepository extends JpaRepository<ExtractEntity, String>, JpaSpecificationExecutor<ExtractEntity> {

    Optional<ExtractEntity> findFirstByProductIdAndPeriodOrderByCreatedAtDesc(String productId, String period);

    Page<ExtractEntity> findByStatus(LoadStatus status, Pageable pageable);

    @Modifying
    @Query("UPDATE ExtractEntity e SET e.percentAdvance = :percent, e.status = :status, e.details = :details, e.updateAt = CURRENT_TIMESTAMP WHERE e.id = :extractId")
    int updateProgressDirectly(@Param("extractId") String extractId,
                               @Param("percent") int percent,
                               @Param("status") LoadStatus status,
                               @Param("details") String details);
}