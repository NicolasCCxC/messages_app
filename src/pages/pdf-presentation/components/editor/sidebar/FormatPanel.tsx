import { useContext } from 'react';
import { IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { FONTS, PAPER_SIZES } from '@constants/Pdf';
import { ChangeEvent } from '@models/Input';
import { EditorContext, IMargins } from '@pages/pdf-presentation/context';
import { DECIMAL_REGEX, MARGIN_FIELDS } from '.';

export const FormatPanel: React.FC = () => {
    const { formatConfig, updateFormatConfig } = useContext(EditorContext);

    const handleMarginChange = ({ target: { name, value } }: ChangeEvent): void => {
        const isValid = DECIMAL_REGEX.test(value);
        if (isValid || value === '') {
            updateFormatConfig({ ...formatConfig, margins: { ...formatConfig.margins, [name]: value } });
        }
    };

    const handleOptionChange = (option: IOption, name = ''): void => {
        updateFormatConfig({ ...formatConfig, [name]: option });
    };

    return (
        <>
            <h3 className="mb-2 text-gray-dark w-max">Formato</h3>
            <SelectSearch
                onChangeOption={handleOptionChange}
                options={PAPER_SIZES}
                label="Tipo de papel"
                value={formatConfig.pageSize.label}
                wrapperClassName="w-[8.3125rem]"
                name="pageSize"
            />
            <div className="my-4.5 w-[7.25rem]">
                <h3 className="mb-1 text-sm text-gray-dark">Margen</h3>
                {MARGIN_FIELDS.map(({ label, name, wrapperClassName }) => (
                    <TextInput
                        key={name}
                        label={label}
                        onChange={handleMarginChange}
                        name={name}
                        value={formatConfig.margins[name as keyof IMargins]}
                        wrapperClassName={wrapperClassName}
                        inputClassName="!w-8"
                        allowDecimals
                        suffix='cm'
                    />
                ))}
            </div>
            <SelectSearch
                label="Tipografía"
                labelClassName="text-gray-dark ml-0"
                onChangeOption={handleOptionChange}
                options={FONTS}
                placeholder="Seleccionar"
                value={formatConfig.font.label}
                wrapperClassName="w-[8.3125rem]"
                name="font"
            />
        </>
    );
};
