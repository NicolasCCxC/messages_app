package avvillas.core.persistence.mapper;

import avvillas.core.persistence.entity.IndexFileEntity;
import avvillas.core.service.dto.index_file.IndexDto;
import org.mapstruct.Mapper;
import org.mapstruct.NullValuePropertyMappingStrategy;

@Mapper(componentModel = "spring", nullValuePropertyMappingStrategy = NullValuePropertyMappingStrategy.IGNORE)
public interface IndexFileMapper {
    IndexDto toDto(IndexFileEntity entity);

    IndexFileEntity toEntity(IndexDto dto);
}
