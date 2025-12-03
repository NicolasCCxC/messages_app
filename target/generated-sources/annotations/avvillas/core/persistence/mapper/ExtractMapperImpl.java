package avvillas.core.persistence.mapper;

import avvillas.core.constant.enums.LoadStatus;
import avvillas.core.persistence.entity.ExtractEntity;
import avvillas.core.service.dto.extract.ExtractDto;
import javax.annotation.processing.Generated;
import org.springframework.stereotype.Component;

@Generated(
    value = "org.mapstruct.ap.MappingProcessor",
    date = "2025-12-03T09:03:21-0500",
    comments = "version: 1.5.5.Final, compiler: javac, environment: Java 22.0.1 (Oracle Corporation)"
)
@Component
public class ExtractMapperImpl implements ExtractMapper {

    @Override
    public ExtractDto toDto(ExtractEntity entity) {
        if ( entity == null ) {
            return null;
        }

        ExtractDto extractDto = new ExtractDto();

        extractDto.setProductId( entity.getProductId() );
        extractDto.setPeriod( entity.getPeriod() );
        if ( entity.getStatus() != null ) {
            extractDto.setStatus( entity.getStatus().name() );
        }
        extractDto.setPercentAdvance( String.valueOf( entity.getPercentAdvance() ) );
        extractDto.setUser( entity.getUser() );

        return extractDto;
    }

    @Override
    public ExtractEntity toEntity(ExtractDto dto) {
        if ( dto == null ) {
            return null;
        }

        ExtractEntity extractEntity = new ExtractEntity();

        extractEntity.setProductId( dto.getProductId() );
        extractEntity.setUser( dto.getUser() );
        extractEntity.setPeriod( dto.getPeriod() );
        if ( dto.getStatus() != null ) {
            extractEntity.setStatus( Enum.valueOf( LoadStatus.class, dto.getStatus() ) );
        }
        if ( dto.getPercentAdvance() != null ) {
            extractEntity.setPercentAdvance( Integer.parseInt( dto.getPercentAdvance() ) );
        }

        return extractEntity;
    }
}
