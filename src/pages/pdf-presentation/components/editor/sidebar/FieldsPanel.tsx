import { useContext, useEffect, useState } from 'react';
import { Button } from '@components/button';
import { TextInput } from '@components/text-input';
import type { IGenericRecord } from '@models/GenericRecord';
import type { ChangeEvent } from '@models/Input';
import { DragAndDropContext } from '@pages/pdf-presentation/context';
import { filterData } from '@utils/Array';
import { FIELD_NAME } from '.';

export const FieldsPanel: React.FC<{ allFields: IGenericRecord[] }> = ({ allFields }) => {
    const { onDragStart, onDragEnd } = useContext(DragAndDropContext).handlers;

    const [filteredFields, setFilteredFields] = useState(allFields);
    const [searchValue, setSearchValue] = useState('');

    useEffect(() => setFilteredFields(allFields), [allFields]);

    const handleSearchChange = ({ target: { value } }: ChangeEvent): void => setSearchValue(value);

    const filterFields = (): void => {
        if (!searchValue) setFilteredFields(allFields);
        setFilteredFields(filterData(allFields, { key: FIELD_NAME, value: searchValue }));
    };

    return (
        <div className="fields-panel">
            <h3 className="text-gray-dark h-[1.125rem]">Campos</h3>
            <TextInput
                isSearch
                onChange={handleSearchChange}
                placeholder="Buscar"
                value={searchValue}
                wrapperClassName="w-[9.625rem] my-2"
            />
            <Button buttonClassName="mb-4.5 mx-[0.125rem]" onClick={filterFields} text="Consultar" />
            <div className="fields-panel__inputs">
                {filteredFields.map(item => (
                    <div
                        role="button"
                        tabIndex={0}
                        className="fields-panel__input"
                        draggable
                        key={item.id}
                        onDragStart={event => onDragStart(event, { ...item, type: 'FIELD' })}
                        onDragEnd={onDragEnd}
                    >
                        {item.fieldName}
                    </div>
                ))}
            </div>
        </div>
    );
};
