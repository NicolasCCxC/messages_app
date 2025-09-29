import { useState, useRef } from 'react';
import { Icon } from '@components/icon';
import { useOutsideClick } from '@hooks/useOutsideClick';
import { ENTER } from '@components/form';
import type { IOption, IFontSizeSelectorProps } from '.';

export const FontSizeSelector: React.FC<IFontSizeSelectorProps> = ({
    inputClassName,
    labelClassName,
    wrapperClassName,
    containerClassName,
    iconClassName,
    label,
    options = [],
    value,
    onChangeOption,
    placeholder,
    error,
    name,
}) => {
    const [isOpen, setIsOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const selectInputRef = useOutsideClick(() => setIsOpen(false));
    const searchInputRef = useRef<HTMLInputElement>(null);

    const toggleDropdown = (): void => {
        setIsOpen(!isOpen);
        if (!isOpen) {
            setSearchQuery('');
        }
    };

    const handleCustomValue = (): void => {
        const trimmedQuery = searchQuery.trim();
        if (!trimmedQuery) return;

        const parsedValue = isNaN(Number(trimmedQuery)) ? trimmedQuery : Number(trimmedQuery);

        const existingOption = options.find(option => option.label.toLowerCase() === trimmedQuery.toLowerCase());

        if (!existingOption) {
            const newOption = { value: parsedValue, label: trimmedQuery };

            onChangeOption(newOption, name);
        } else {
            onChangeOption(existingOption, name);
        }

        setSearchQuery('');
        setIsOpen(false);
    };

    const handleSelectOption = (option: IOption): void => {
        onChangeOption(option, name);
        setSearchQuery('');
        setIsOpen(false);
    };

    const filteredOptions = options.filter(option => {
        const query = searchQuery.toLowerCase();
        const label = option.label.toLowerCase();
        return label.includes(query);
    });

    const selectedValue = options.find(option => option.id === value)?.label;

    let displayValue: string | undefined;

    if (selectedValue && selectedValue !== '') displayValue = selectedValue ?? '';
    else if (value && value !== '') displayValue = value ?? '';
    else displayValue = placeholder;

    return (
        <div className={`relative ${wrapperClassName}`} ref={selectInputRef}>
            {label && (
                <p className={`min-w-[3.9375rem] h-[1rem] text-left ml-[0.625rem] text-sm mb-1 text-black ${labelClassName}`}>
                    {label}
                </p>
            )}
            <div
                role="button"
                tabIndex={0}
                className={`flex justify-between cursor-pointer px-2.5 rounded border bg-white ${containerClassName} ${
                    error ? 'border-red-error' : isOpen ? 'border-blue-light' : 'border-gray-dark'
                }`}
                onKeyDown={e => e.key === ENTER && toggleDropdown()}
                onClick={toggleDropdown}
            >
                {isOpen ? (
                    <input
                        ref={searchInputRef}
                        type="text"
                        value={searchQuery}
                        onChange={e => setSearchQuery(e.target.value)}
                        onKeyDown={e => {
                            if (e.key === ENTER) {
                                e.preventDefault();
                                handleCustomValue();
                            }
                        }}
                        className="w-full text-sm rounded focus:outline-none focus:border-none"
                        placeholder="Buscar..."
                        onClick={e => e.stopPropagation()}
                    />
                ) : (
                    <p
                        className={`overflow-hidden text-sm overflow-ellipsis text-nowrap ${
                            !value && placeholder ? 'text-gray-dark' : 'text-black'
                        } ${inputClassName}`}
                    >
                        {displayValue}
                    </p>
                )}
                <Icon name="arrowDown" className={`ml-2 transition-transform ${iconClassName} ${isOpen ? 'rotate-180' : ''}`} />
            </div>

            {isOpen && (
                <div className="absolute z-10 w-full overflow-hidden text-[0.8125rem] text-black bg-white border border-t-0 rounded-b border-blue-light">
                    <ul className="overflow-y-auto max-h-40">
                        {filteredOptions.length > 0 &&
                            filteredOptions.map((option: IOption, index) => (
                                <div
                                    key={option.value}
                                    className={`px-1.5 hover:text-white hover:bg-blue-light h-9 flex justify-center ${
                                        value === option.value ? 'text-black' : 'text-gray-dark'
                                    }`}
                                >
                                    <li
                                        role="button"
                                        tabIndex={0}
                                        onKeyDown={e => e.key === ENTER && handleSelectOption(option)}
                                        onClick={() => handleSelectOption(option)}
                                        className={`cursor-pointer flex items-center w-full min-h-[1rem] ${
                                            filteredOptions.length === index + 1 ? '' : 'border-b border-[#000]'
                                        }`}
                                    >
                                        {option.label}
                                    </li>
                                </div>
                            ))}
                    </ul>
                </div>
            )}
        </div>
    );
};
