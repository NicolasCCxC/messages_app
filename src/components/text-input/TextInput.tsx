import React, { useRef, useState } from 'react';
import { Icon } from '@components/icon';
import { useOutsideClick } from '@hooks/useOutsideClick';
import { ENTER } from '@components/form';
import { ChangeEvent } from '@models/Input';
import { ITextInputProps, INVALID_NUMBER_CHARS, FORBIDDEN_NUMERIC_CHARS, DECIMAL_OPTION } from '.';

export const TextInput: React.FC<ITextInputProps> = ({
    name,
    value = '',
    label,
    onChange = (): void => {},
    placeholder,
    inputClassName: inputExtraClassName,
    labelClassName,
    wrapperClassName,
    error,
    isSearch,
    maxLength,
    type,
    inputWrapperClassName: inputWrapperExtraClassName,
    disabled,
    allowDecimals = false,
    suffix = '',
}) => {
    const [isFocus, setIsFocus] = useState(false);
    const inputRef = useRef<HTMLInputElement | null>(null);
    const selectInputRef = useOutsideClick(() => setIsFocus(false));
    const showPlaceholder = !isFocus && !value && placeholder;

    const handleFocusInput = (): void => {
        setIsFocus(true);
        inputRef.current?.focus();
    };

    const handleChange = (e: ChangeEvent): void => {
        const isOverMaxLength = type === 'number' && maxLength && e.target.value.length > maxLength;
        if (isOverMaxLength) return;
        onChange(e);
    };

    const inputType = allowDecimals ? DECIMAL_OPTION.WITH_DECIMALS : DECIMAL_OPTION.NO_DECIMALS;

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>): void => {
        if (INVALID_NUMBER_CHARS[inputType].includes(e.key)) e.preventDefault();
    };

    const handlePaste = (e: React.ClipboardEvent<HTMLInputElement>): void => {
        const paste = e.clipboardData.getData('text');
        if (FORBIDDEN_NUMERIC_CHARS[inputType].test(paste)) e.preventDefault();
    };

    const getBorderColorClass = (): string => {
        if (error) return 'border-red-error';
        else if (isFocus) return 'border-blue-light';
        else return 'border-gray-dark';
    };

    const inputClassName = `w-full text-sm focus:outline-none text-nowrap bg-transparent ${inputExtraClassName} ${
        isSearch ? 'h-[1.875rem]' : ''
    } ${disabled ? 'text-gray-dark' : 'text-black'}`;

    const inputWrapperClassName = ` flex justify-between items-center px-2.5 rounded border ${getBorderColorClass()} ${
        isSearch ? 'h-[1.875rem]' : ''
    } ${inputWrapperExtraClassName} ${disabled ? 'bg-gray-light border-gray-dark' : 'bg-white'}`;

    return (
        <div ref={selectInputRef} className={`flex flex-col ${wrapperClassName}`}>
            {label && (
                <label
                    className={`min-w-[3.9375rem] text-left ml-[0.625rem] text-sm mb-1 text-black leading-4 ${labelClassName}`}
                >
                    {label}
                </label>
            )}
            <div
                role="button"
                tabIndex={0}
                onKeyDown={e => e.key === ENTER && handleFocusInput}
                onClick={handleFocusInput}
                className={inputWrapperClassName}
            >
                <div className="relative flex items-center w-full">
                    {showPlaceholder && (
                        <label
                            className={`cursor-text absolute line-clamp-2 w-full overflow-hidden overflow-ellipsis leading-[0.875rem] text-gray-dark ${
                                isSearch ? 'text-[0.8125rem]' : 'text-sm'
                            }`}
                        >
                            {placeholder}
                        </label>
                    )}
                    <span className="flex w-full">
                        <input
                            autoComplete="off"
                            disabled={disabled}
                            name={name}
                            ref={inputRef}
                            value={value}
                            onChange={handleChange}
                            className={inputClassName}
                            maxLength={maxLength}
                            type={type}
                            {...(type === 'number' && {
                                onKeyDown: handleKeyDown,
                                onPaste: handlePaste,
                            })}
                        />
                        <span className="text-black">{suffix}</span>
                    </span>
                </div>
                {isSearch && <Icon name="search" className="ml-2 w-[1.375rem] h-[1.375rem] cursor-default" />}
            </div>
        </div>
    );
};
