import { useContext } from 'react';
import { ChangeEvent } from '@models/Input';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { Icon } from '@components/icon';
import { PLACEHOLDERS } from '@constants/ObjectsEditor';
import { SizeControls } from './SizeControls';
import { SHAPE_STYLE_TOOLS } from '.';
import ColorPicker from './ColorPicker';

export const ShapeTool: React.FC = () => {
    const { element, updateElementStyles } = useContext(ManageObjectContext);

    const handleChangeStyle = ({ target: { value } }: ChangeEvent, styleValue: keyof React.CSSProperties): void =>
        updateElementStyles(styleValue, value);

    return (
        <div>
            <p className="mb-2 text-sm text-black">Fondo</p>
            <p className="mb-2 text-xs text-black">Color</p>
            <ColorPicker value={element.style?.backgroundColor as string} onChange={e => handleChangeStyle(e, 'backgroundColor')} />
            <p className="mb-2 text-sm text-black">Color de borde</p>
            <ColorPicker value={element.style?.borderColor as string} onChange={e => handleChangeStyle(e, 'borderColor')} />
            <p className="mb-1 text-xs text-black">Borde redondo</p>
            <div className="flex flex-wrap w-[8.75rem] mb-4.5">
                {SHAPE_STYLE_TOOLS.map(({ iconName, styleValue }) => (
                    <div
                        key={styleValue}
                        className="w-[3.75rem] mb-2 mr-2 h-[1.5625rem] flex justify-center items-center rounded bg-white"
                    >
                        <Icon name={iconName} />
                        <input
                            type="number"
                            placeholder={PLACEHOLDERS.shapeStyle}
                            className="w-4 h-4 ml-2 text-sm text-black"
                            name={styleValue}
                            onChange={e => handleChangeStyle(e, styleValue)}
                            value={element?.style?.[styleValue] ?? ''}
                        />
                    </div>
                ))}
            </div>
            <SizeControls />
        </div>
    );
};
