import { ReactNode, useState } from 'react';
import { Icon } from '@components/icon';
import { IOption, IFontSizeSelectorProps, FontSizeSelector as ImportedFontSizeSelector } from '@components/font-size-selector';
import { ChangeEvent } from '@models/Input';
import { PLACEHOLDERS } from '@constants/ObjectsEditor';
import { ENTER } from '@components/form';
import { FONT_SIZE_OPTIONS, TEXT_STYLE_TOOLS } from '../../tools';
import { TableToolbarProps } from '.';

export const Toolbar: React.FC<TableToolbarProps> = ({ selectedCell, updateSelectedCellStyle, handleMergeCells }) => {
    if (!selectedCell) return null;

    return (
        <div className="w-[19.875rem] py-0.5 pr-1 pl-2 h-[1.875rem] rounded bg-white shadow-default mb-1 flex items-center justify-between">
            <ColorPicker onChange={e => updateSelectedCellStyle({ backgroundColor: e.target.value })} />

            <Icon name="mergeCell" className="mr-2 ml-1 w-5 h-[1.125rem]" onClick={handleMergeCells} />

            <FontSizeSelector
                selectedCell={selectedCell}
                onChangeOption={(option: IOption) => updateSelectedCellStyle({ fontSize: option.value })}
            />

            <TextStyleTools updateStyle={updateSelectedCellStyle} />

            <ColorPicker name="color" onChange={(e: ChangeEvent) => updateSelectedCellStyle({ color: e.target.value })} />
        </div>
    );
};

const ColorPicker = ({
    name = 'bg',
    onChange,
}: {
    name?: string;
    onChange?: React.ChangeEventHandler<HTMLInputElement>;
}): ReactNode => <input name={name} type="color" className="w-5 h-5 cursor-pointer" onChange={onChange} />;

const FontSizeSelector = ({
    onChangeOption,
    selectedCell,
}: {
    onChangeOption: IFontSizeSelectorProps['onChangeOption'];
    selectedCell: TableToolbarProps['selectedCell'];
}): ReactNode => {
    return (
        <ImportedFontSizeSelector
            value={selectedCell?.style.fontSize}
            onChangeOption={onChangeOption}
            options={FONT_SIZE_OPTIONS}
            wrapperClassName="w-[3.375rem] h-[1.625rem]"
            iconClassName="!m-0 w-[1.125rem] h-[1.5625rem]"
            containerClassName="!px-1 border-blue"
            placeholder={PLACEHOLDERS.fontSize}
        />
    );
};

const TextStyleTools = ({ updateStyle }: { updateStyle: TableToolbarProps['updateSelectedCellStyle'] }): ReactNode => {
    const [activeStyle, setActiveStyle] = useState<{ [key: string]: string | number | undefined }>({});

    const handleStyleClick = (styleValue: string, value: string | number): void => {
        const isActive = activeStyle[styleValue] === value;
        const newStyle = { ...activeStyle, [styleValue]: isActive ? '' : value };

        setActiveStyle(newStyle);
        updateStyle(newStyle);
    };

    return (
        <div className="w-[7.5rem] h-[1.625rem] mx-2 flex items-center justify-between border border-blue rounded">
            {TEXT_STYLE_TOOLS.map(({ value, Icon, styleValue }) => (
                <div
                    role="button"
                    key={value}
                    tabIndex={0}
                    onKeyDown={e => e.key === ENTER && handleStyleClick(styleValue, value)}
                    onClick={() => handleStyleClick(styleValue, value)}
                >
                    <Icon active={false} />
                </div>
            ))}
        </div>
    );
};
