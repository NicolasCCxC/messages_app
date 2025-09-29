import { useState } from 'react';
import { useOutsideClick } from '@hooks/useOutsideClick';
import { ENTER } from '@components/form';
import { Icon } from '@components/icon';
import { IMultiSelectProps } from '.';

export const MultiSelect: React.FC<IMultiSelectProps> = ({
    options,
    handleChangeOption,
    selectedOptions,
    label,
    wrapperClassName,
    inputClassName,
}) => {
    const [isOpen, setIsOpen] = useState(false);

    const selectInputRef = useOutsideClick(() => setIsOpen(false));

    return (
        <div className={`relative ${wrapperClassName}`} ref={selectInputRef}>
            {label && <p className="min-w-[3.9375rem] h-[1rem] text-left ml-[0.625rem] text-sm mb-1 text-black">{label}</p>}
            <div
                role="button"
                tabIndex={0}
                className={`flex justify-between bg-white rounded cursor-pointer ${inputClassName}`}
                onClick={() => setIsOpen(!isOpen)}
                onKeyDown={e => {
                    if (e.key === ENTER) setIsOpen(!isOpen);
                }}
            >
                <p className={selectedOptions.length > 0 ? 'text-black' : ' text-gray-dark'}>
                    {selectedOptions.length > 0 ? selectedOptions.map(item => item.description).join(', ') : 'Seleccionar'}
                </p>
                <Icon name="arrowDown" className={`ml-2 transition-transform ${isOpen ? 'rotate-180' : ''}`} />
            </div>
            {isOpen && (
                <div className="absolute w-[8.375rem] bg-[#ECF0F1] h-[6.25rem] left-0 right-0 p-2.5 mt-1rounded shadow-lg">
                    {options.map(({ description, code }, index) => (
                        <label
                            key={code}
                            className={`${
                                selectedOptions.some(item => item.description === description) ? 'text-black' : 'text-gray-dark'
                            } ${
                                options.length > index + 1 ? 'border-b border-[#000000]' : ''
                            } flex  items-center gap-2 py-1 cursor-pointer text-[0.8125rem]`}
                        >
                            <span
                                className={`w-4 h-4 border border-[#000000] ${
                                    selectedOptions.some(item => item.description === description) ? 'bg-blue-light' : 'bg-gray'
                                }`}
                            />
                            <input
                                type="checkbox"
                                checked={selectedOptions.some(item => item.description === description)}
                                onChange={() => handleChangeOption({ description, code })}
                                className="hidden"
                            />
                            {description}
                        </label>
                    ))}
                </div>
            )}
        </div>
    );
};
