package avvillas.core.persistence.repository;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.IndexFileEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface IndexFileRepository extends JpaRepository<IndexFileEntity, String>, JpaSpecificationExecutor<IndexFileEntity> {

    Optional<IndexFileEntity> findFirstByProductIdAndPeriodAndStatusOrderByCreatedAtDesc(String productId, String period, LoadStatus status);
}