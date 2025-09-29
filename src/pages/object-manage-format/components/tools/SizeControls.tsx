import React, { CSSProperties, useContext } from 'react';
import { TextInput } from '@components/text-input';
import { ChangeEvent } from '@models/Input';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { PLACEHOLDERS } from '@constants/ObjectsEditor';

export const SizeControls: React.FC = () => {
    const { updateElementStyles, element } = useContext(ManageObjectContext);
    const handleImageSize = (name: keyof CSSProperties, value: number): void => updateElementStyles(name, value);

    return (
        <div>
            <p className="mb-1 text-sm text-black">Tamaño</p>
            <div className="flex">
                <TextInput
                    label="Alto"
                    type="number"
                    wrapperClassName="mr-1 w-[5.5625rem]"
                    placeholder={PLACEHOLDERS.sizeControl}
                    maxLength={3}
                    value={element.style?.height as string}
                    onChange={(e: ChangeEvent) => handleImageSize('height', parseInt(e.target.value))}
                />
                <TextInput
                    label="Ancho"
                    type="number"
                    wrapperClassName="w-[5.5rem]"
                    placeholder={PLACEHOLDERS.sizeControl}
                    maxLength={3}
                    value={element.style?.width as string}
                    onChange={(e: ChangeEvent) => handleImageSize('width', parseInt(e.target.value))}
                />
            </div>
        </div>
    );
};
