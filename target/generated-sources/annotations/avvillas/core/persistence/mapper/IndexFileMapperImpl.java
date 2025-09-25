package avvillas.core.persistence.mapper;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.service.dto.index_file.IndexDto;
import javax.annotation.processing.Generated;
import org.springframework.stereotype.Component;

@Generated(
    value = "org.mapstruct.ap.MappingProcessor",
    date = "2025-09-25T12:34:16-0500",
    comments = "version: 1.5.5.Final, compiler: javac, environment: Java 21.0.6 (Amazon.com Inc.)"
)
@Component
public class IndexFileMapperImpl implements IndexFileMapper {

    @Override
    public IndexDto toDto(IndexFileEntity entity) {
        if ( entity == null ) {
            return null;
        }

        IndexDto indexDto = new IndexDto();

        indexDto.setProductId( entity.getProductId() );
        indexDto.setPeriod( entity.getPeriod() );
        if ( entity.getStatus() != null ) {
            indexDto.setStatus( entity.getStatus().name() );
        }
        indexDto.setUser( entity.getUser() );
        indexDto.setPercentAdvance( entity.getPercentAdvance() );
        indexDto.setClientsProcessed( entity.getClientsProcessed() );
        indexDto.setCreatedAt( entity.getCreatedAt() );

        return indexDto;
    }

    @Override
    public IndexFileEntity toEntity(IndexDto dto) {
        if ( dto == null ) {
            return null;
        }

        IndexFileEntity indexFileEntity = new IndexFileEntity();

        indexFileEntity.setCreatedAt( dto.getCreatedAt() );
        indexFileEntity.setProductId( dto.getProductId() );
        indexFileEntity.setPeriod( dto.getPeriod() );
        indexFileEntity.setUser( dto.getUser() );
        if ( dto.getStatus() != null ) {
            indexFileEntity.setStatus( Enum.valueOf( LoadStatus.class, dto.getStatus() ) );
        }
        indexFileEntity.setClientsProcessed( dto.getClientsProcessed() );
        if ( dto.getPercentAdvance() != null ) {
            indexFileEntity.setPercentAdvance( dto.getPercentAdvance() );
        }

        return indexFileEntity;
    }
}
