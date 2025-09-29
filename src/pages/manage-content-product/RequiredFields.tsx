import { TextInput } from '@components/text-input';
import type { ChangeEvent } from '@models/Input';
import { type IOption, SelectSearch } from '@components/select-search';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { MAX_LENGHT_FIELD, RequiredFieldsProps } from '.';
import { ENTER } from '@components/form';

export const RequiredFields: React.FC<RequiredFieldsProps> = ({ sendModal, fields, updateField, onAddField, options }) => {
    const handleTextChange = ({ target: { value } }: ChangeEvent, index: number): void => {
        updateField(index, { content: value });
    };

    const handleCheckChange = ({ target: { checked } }: ChangeEvent, index: number): void => {
        updateField(index, {
            isFixed: checked,
            ...(checked ? { inputProductStructureId: null } : { content: null }),
        });
    };

    const handleSelectChange = (option: IOption, index: number): void => {
        updateField(index, {
            inputProductStructureId: option.value as string,
            content: null,
        });
    };

    const getSelectedOption = (fieldId: string | null): IOption | undefined => {
        if (!fieldId) return undefined;
        return options.find(option => option.value === fieldId);
    };

    const usedOptions = new Set(fields.map(field => field.inputProductStructureId).filter((id): id is string => id !== null));

    return (
        <>
            <div className="max-h-[12.0313rem] overflow-y-auto">
                {fields.map((field, index) => (
                    <div key={field.id} className="flex items-end w-[16.5625rem] mb-4.5">
                        {field.isFixed ? (
                            <TextInput
                                name={`field-${index}`}
                                label="Campos requeridos"
                                placeholder={DEFAULT_PLACEHOLDER}
                                wrapperClassName="w-[13.5625rem]"
                                inputClassName="h-[1.5rem]"
                                onChange={e => handleTextChange(e, index)}
                                error={sendModal && !field.content}
                                value={field.content ?? ''}
                                maxLength={MAX_LENGHT_FIELD.fixedRequiredfile}
                            />
                        ) : (
                            <SelectSearch
                                value={getSelectedOption(field.inputProductStructureId)?.label ?? ''}
                                options={options.filter(opt => !usedOptions.has(String(opt.value)))}
                                placeholder={DEFAULT_PLACEHOLDER}
                                onChangeOption={option => handleSelectChange(option, index)}
                                label="Campos requeridos"
                                wrapperClassName="w-[13.5625rem]"
                                inputClassName="h-[1.4375rem]"
                                error={sendModal && !getSelectedOption(field.inputProductStructureId)?.value}
                            />
                        )}

                        <label
                            className={`w-4 h-4 mb-0.5 mx-1 rounded cursor-pointer border border-blue-light ${
                                field.isFixed ? 'bg-blue-light' : 'bg-gray-light'
                            }`}
                            htmlFor={`check-${index}`}
                        >
                            <input
                                type="checkbox"
                                id={`check-${index}`}
                                className="hidden"
                                name={`check-${index}`}
                                checked={field.isFixed}
                                onChange={e => handleCheckChange(e, index)}
                            />
                        </label>
                        <span className="text-sm text-gray-dark">Fijo</span>
                    </div>
                ))}
            </div>
            <p
                tabIndex={0}
                role="button"
                className="text-sm underline cursor-pointer mb-4.5 text-blue-light"
                onKeyDown={e => e.key === ENTER && onAddField()}
                onClick={onAddField}
            >
                +Agregar campo
            </p>
        </>
    );
};
