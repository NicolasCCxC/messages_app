import { useCallback, useContext, useState } from 'react';
import { IGenericRecord } from '@models/GenericRecord';
import { FieldType } from '@models/Table';
import { isEmptyValue } from '@utils/Array';
import { TableContext } from '../context';
import { FIELD_TYPES, Icons, IBodyProps } from '.';

export const Body: React.FC<IBodyProps> = ({ customIcons, fields, onFieldChange, requiredFields = [] }) => {
    const [validate, setValidate] = useState(false);
    const { data } = useContext(TableContext);

    const getCellClass = ({ activeItem, name, ...item }: IGenericRecord, isEditable: boolean): string => {
        const isRequired = validate && activeItem && requiredFields.includes(name) && isEmptyValue(item?.[name]);

        return `border-b ${isRequired ? 'border-red-error border' : 'border-gray'} px-2 h-[2.3125rem] ${
            isEditable ? 'bg-white' : 'bg-gray-light'
        }`;
    };

    const updateValidate = useCallback((validate: boolean): void => setValidate(validate), []);

    return (
        <tbody>
            {data?.current?.map((item, row) => (
                <tr className="text-sm" key={item.id}>
                    {fields.map((field, index: number) => {
                        const { type = FieldType.Text, name, icons, editable = true, validatePattern } = field;
                        const key = `name-${name}-${index}`;
                        const fullItem = { ...item, ...field };
                        const isEditable = editable && item?.edit;
                        const isBlocked = item.blocked && field.name === 'code';

                        if (type === FieldType.Icons) {
                            return (
                                <td key={key}>
                                    {customIcons ? (
                                        customIcons(fullItem)
                                    ) : (
                                        <Icons item={fullItem} list={icons} updateValidate={updateValidate} />
                                    )}
                                </td>
                            );
                        }

                        const Component = FIELD_TYPES[type];

                        return (
                            <td key={key} className={getCellClass(fullItem, isEditable && !isBlocked)}>
                                <Component
                                    handleChange={(value: string) => {
                                        if (validatePattern && !validatePattern.test(value)) return;
                                        onFieldChange(value, { item: fullItem, row });
                                    }}
                                    isEditable={isEditable && !isBlocked}
                                    item={fullItem}
                                />
                            </td>
                        );
                    })}
                </tr>
            ))}
        </tbody>
    );
};
