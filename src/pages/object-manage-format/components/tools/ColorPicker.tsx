import React, { ChangeEventHandler, useRef, useState } from 'react';
import { useOutsideClick } from '@hooks/useOutsideClick';
import { Icon } from '@components/icon';

interface ColorPickerProps {
    value: string;
    onChange: ChangeEventHandler<HTMLInputElement>;
}

const ColorPicker: React.FC<ColorPickerProps> = ({ value, onChange }) => {
    const selectInputRef = useOutsideClick(() => setIsOpen(false));
    const [isOpen, setIsOpen] = useState(false);
    const colorInput = useRef<HTMLInputElement>(null);

    const toggleOpen = (): void => {
        setIsOpen(!isOpen);
    };

    return (
        <div
            className={`relative w-[12.25rem] mb-2 h-[1.5625rem] bg-white cursor-pointer border rounded flex items-center px-2 ${
                isOpen ? 'border-blue-light' : 'border-gray-dark'
            }`}
            onClick={toggleOpen}
            ref={selectInputRef}
        >
            <div className="w-4 h-4 border border-gray-400 rounded-sm" style={{ backgroundColor: value }}></div>
            <span className={`flex-1 ml-2 text-sm ${!value ? 'text-gray-dark' : 'text-black'}`}>{value || '#FFFFFF'}</span>
            <Icon name="arrowDown" className={`ml-2 transition-transform ${isOpen ? 'rotate-180' : ''}`} />
            <input
                ref={colorInput}
                type="color"
                value={value}
                onChange={onChange}
                className="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
            />
        </div>
    );
};

export default ColorPicker;
