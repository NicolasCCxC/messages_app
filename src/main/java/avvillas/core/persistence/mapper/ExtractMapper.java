package avvillas.core.persistence.mapper;

import avvillas.core.persistence.entity.ExtractEntity;
import avvillas.core.service.dto.extract.ExtractDto;
import org.mapstruct.Mapper;
import org.mapstruct.NullValuePropertyMappingStrategy;

@Mapper(componentModel = "spring", nullValuePropertyMappingStrategy = NullValuePropertyMappingStrategy.IGNORE)
public interface ExtractMapper {
    ExtractDto toDto(ExtractEntity entity);

    ExtractEntity toEntity(ExtractDto dto);
}
