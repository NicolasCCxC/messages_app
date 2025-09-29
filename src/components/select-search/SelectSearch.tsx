import { useState, useRef, useEffect } from 'react';
import { Icon } from '@components/icon';
import { useOutsideClick } from '@hooks/useOutsideClick';
import type { IOption, ISelectSearchProps } from '.';
import { ENTER } from '@components/form';

export const SelectSearch: React.FC<ISelectSearchProps> = ({
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

    useEffect(() => {
        if (isOpen && searchInputRef.current) {
            setTimeout(() => {
                searchInputRef.current?.focus();
            }, 0);
        }
    }, [isOpen]);

    const filteredOptions = options.filter(option => option.label.toLowerCase().includes(searchQuery.toLowerCase()));

    const selectedValue = options.find(option => option.id === value)?.label;

    const createContainerClasses = (): string => {
        const classes = 'flex justify-between cursor-pointer px-2.5 rounded border bg-white';

        if (error) {
            return `${classes} border-red-error`;
        } else if (isOpen) {
            return `${classes} border-blue-light`;
        } else {
            return `${classes} border-gray-dark`;
        }
    };

    let displayValue: string | undefined;

    if (selectedValue && selectedValue !== '') displayValue = selectedValue ?? '';
    else if (value && value !== '') displayValue = value ?? '';
    else displayValue = placeholder;

    return (
        <div className={`relative ${wrapperClassName}`} ref={selectInputRef}>
            <div
                role="button"
                onKeyDown={e => {
                    if (e.key === ENTER) toggleDropdown();
                }}
                tabIndex={0}
                onClick={toggleDropdown}
            >
                {label && (
                    <p className={`min-w-[3.9375rem] h-[1rem] text-left ml-[0.625rem] text-sm mb-1 text-black ${labelClassName}`}>
                        {label}
                    </p>
                )}
                <div className={`${containerClassName} ${createContainerClasses()}`}>
                    {isOpen ? (
                        <input
                            ref={searchInputRef}
                            type="text"
                            value={searchQuery}
                            onChange={e => setSearchQuery(e.target.value)}
                            className="w-full text-sm rounded focus:outline-none focus:border-none"
                            placeholder="Buscar..."
                            onClick={e => e.stopPropagation()}
                        />
                    ) : (
                        <p
                            className={`overflow-hidden text-sm cursor-pointer overflow-ellipsis text-nowrap ${inputClassName} ${
                                !value && placeholder ? 'text-gray-dark' : 'text-black'
                            }`}
                        >
                            {displayValue}
                        </p>
                    )}
                    <Icon
                        name="arrowDown"
                        className={`ml-2 transition-transform ${iconClassName} ${isOpen ? 'rotate-180' : ''}`}
                    />
                </div>
            </div>
            {isOpen && (
                <div className="absolute z-10 w-full overflow-hidden text-[0.8125rem] text-black bg-white border border-t-0 rounded-b border-blue-light">
                    <ul className="overflow-y-auto max-h-40">
                        {filteredOptions.length > 0 ? (
                            filteredOptions.map((option: IOption, index) => (
                                <div
                                    key={option.value}
                                    className={`${
                                        value === option.label ? 'text-black' : 'text-gray-dark'
                                    } px-1.5 hover:text-white hover:bg-blue-light h-9 flex justify-center`}
                                >
                                    <li
                                        role="button"
                                        onClick={() => {
                                            onChangeOption(option, name);
                                            toggleDropdown();
                                        }}
                                        onKeyDown={e => {
                                            if (e.key === ENTER) {
                                                onChangeOption(option, name);
                                                toggleDropdown();
                                            }
                                        }}
                                        className={`cursor-pointer flex items-center w-full min-h-[1rem]  ${
                                            filteredOptions.length === index + 1 ? '' : 'border-b border-[#000]'
                                        }`}
                                    >
                                        {option.label}
                                    </li>
                                </div>
                            ))
                        ) : (
                            <li className="px-1 py-2 text-gray-dark">No se encontraron resultados</li>
                        )}
                    </ul>
                </div>
            )}
        </div>
    );
};
