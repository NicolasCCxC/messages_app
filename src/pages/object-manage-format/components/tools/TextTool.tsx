import { CSSProperties, useCallback, useContext, useEffect, useRef, useState } from 'react';
import { ChangeEvent } from '@models/Input';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { IOption, FontSizeSelector } from '@components/font-size-selector';
import { ENTER } from '@components/form';
import { PLACEHOLDERS } from '@constants/ObjectsEditor';
import { BulletedListText, NumberedTextList } from './Icons';
import { Align, FONT_SIZE_OPTIONS, TextStyle, TEXT_ALIGN_TOOLS, TEXT_STYLE_TOOLS } from '.';
import ColorPicker from './ColorPicker';

export const TextTool: React.FC = () => {
    const { element, updateElementStyles, updateElementProperties } = useContext(ManageObjectContext);
    const [activeListType, setActiveListType] = useState<'bullet' | 'numbered' | null>(null);
    const originalTextRef = useRef<string>('');

    const isBulletList = (lines: string[]): boolean => lines.every(line => line.trim().startsWith('•'));

    const isNumberedList = (lines: string[]): boolean => lines.every((line, index) => line.trim().startsWith(`${index + 1}.`));

    const stripBullet = (lines: string[]): string[] => lines.map(line => line.replace(/^•\s*/, ''));

    const stripNumbering = (lines: string[]): string[] => lines.map(line => line.replace(/^\d+\.\s*/, ''));

    const addBullets = (lines: string[]): string[] => lines.map(line => `• ${line}`);

    const addNumbering = (lines: string[]): string[] => lines.map((line, index) => `${index + 1}. ${line}`);

    const getLinesFromContent = (content?: string): string[] => content?.split('\n') || [];

    const getLines = useCallback(() => getLinesFromContent(element?.content), [element?.content]);

    const updateToOriginalText = (): void => {
        updateElementProperties(
            'content',
            originalTextRef.current !== '' ? originalTextRef.current ?? element.content ?? '' : element.content ?? ''
        );
        setActiveListType(null);
    };

    useEffect(() => {
        const lines = getLines();
        if (isBulletList(lines)) {
            setActiveListType('bullet');
        } else if (isNumberedList(lines)) {
            setActiveListType('numbered');
        } else {
            setActiveListType(null);
        }
    }, [element?.content, getLines]);

    const handleTextAlign = (align: Align): void => updateElementStyles('textAlign', align);

    const handleTextSize = (option: IOption): void => updateElementStyles('fontSize', option.value);

    const handleTextColor = ({ target: { value } }: ChangeEvent): void => updateElementStyles('color', value);

    const handleTextStyle = (style: keyof CSSProperties, value: TextStyle, active: boolean): void =>
        updateElementStyles(style, active ? '' : value);

    const formatAsBulletList = (): void => {
        const lines = getLines();

        if (isBulletList(lines)) {
            updateToOriginalText();
        } else {
            const cleanText = isNumberedList(lines) ? stripNumbering(lines).join('\n') : element.content ?? '';
            originalTextRef.current = cleanText;

            const formatted = addBullets(cleanText.split('\n')).join('\n');
            updateElementProperties('content', formatted);
            setActiveListType('bullet');
        }
    };

    const formatAsNumberedList = (): void => {
        const lines = getLines();

        if (isNumberedList(lines)) {
            updateToOriginalText();
        } else {
            const cleanText = isBulletList(lines) ? stripBullet(lines).join('\n') : element.content ?? '';
            originalTextRef.current = cleanText;

            const formatted = addNumbering(cleanText.split('\n')).join('\n');
            updateElementProperties('content', formatted);
            setActiveListType('numbered');
        }
    };

    return (
        <div>
            <div>
                <p className="mb-1 text-sm text-black">Tamaño</p>
                <FontSizeSelector
                    value={element.style?.fontSize as string}
                    onChangeOption={handleTextSize}
                    options={FONT_SIZE_OPTIONS}
                    placeholder={PLACEHOLDERS.sizeControl}
                />
            </div>
            <div className="mt-4.5">
                <p className="mb-1 text-sm text-black">Alineación</p>
                <div className="w-[7.5rem] h-[1.625rem] bg-white rounded flex justify-between px-1 py-0.5 border-gray-dark border">
                    {TEXT_ALIGN_TOOLS.map(({ value, Icon }) => (
                        <div
                            role="button"
                            tabIndex={0}
                            key={value}
                            onKeyDown={e => e.key === ENTER && handleTextAlign(value)}
                            onClick={() => handleTextAlign(value)}
                        >
                            <Icon active={value === element.style?.textAlign} />
                        </div>
                    ))}
                </div>
            </div>
            <div className="mt-4.5">
                <p className="mb-1 text-sm text-black">Color</p>
                <ColorPicker value={element.style?.color as string} onChange={handleTextColor} />
            </div>
            <div className="mt-4.5 flex flex-col">
                <div className="mb-4.5">
                    <p className="mb-1 text-sm text-black">Estilos</p>
                    <div className="mt-1 w-[7.5rem] h-[1.625rem] bg-white rounded flex justify-between px-1 py-0.5 border-gray-dark border">
                        {TEXT_STYLE_TOOLS.map(({ value, Icon, styleValue }) => {
                            const active = value === element.style?.[styleValue as keyof CSSProperties];
                            return (
                                <div
                                    tabIndex={0}
                                    role="button"
                                    key={value}
                                    onClick={() => handleTextStyle(styleValue as keyof CSSProperties, value as TextStyle, active)}
                                    onKeyDown={e =>
                                        e.key === ENTER &&
                                        handleTextStyle(styleValue as keyof CSSProperties, value as TextStyle, active)
                                    }
                                >
                                    <Icon active={active} />
                                </div>
                            );
                        })}
                    </div>
                </div>
                <div>
                    <p className="mb-1 text-sm text-black">Lista</p>
                    <div className="mt-1 w-[3.75rem] h-[1.625rem] bg-white rounded flex justify-between px-1 py-0.5 border-gray-dark border">
                        <div
                            role="button"
                            tabIndex={0}
                            onClick={formatAsBulletList}
                            onKeyDown={e => e.key === ENTER && formatAsBulletList()}
                        >
                            <BulletedListText active={activeListType === 'bullet'} />
                        </div>
                        <div
                            role="button"
                            tabIndex={0}
                            onClick={formatAsNumberedList}
                            onKeyDown={e => e.key === ENTER && formatAsNumberedList()}
                        >
                            <NumberedTextList active={activeListType === 'numbered'} />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
