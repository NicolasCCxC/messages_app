import type { IGenericRecord } from '@models/GenericRecord';

export { TextInput } from './TextInput';

/**
 * This contains the different types of input
 */
type InputType = 'text' | 'number' | 'password' | 'email' | 'date' | 'submit' | 'reset' | 'button';

/**
 * Interface for the TextInput component props
 *
 * @typeParam value: string | number - The current value of the input field
 * @typeParam onChange: React.ChangeEventHandler<HTMLInputElement> - Optional function to handle changes in the input value
 * @typeParam placeholder: string - Optional placeholder text for the input field
 * @typeParam inputClassName: string - Optional prop to apply custom CSS classes to the input element
 * @typeParam labelClassName: string - Optional prop to apply custom CSS classes to the label element
 * @typeParam wrapperClassName: string - Optional prop to apply custom CSS classes to the wrapper element
 * @typeParam label: string - Optional label text to be displayed alongside the input
 * @typeParam error: boolean - Optional flag to indicate if the input is in an error state
 * @typeParam isSearch: boolean - Optional flag indicating whether the input is of the search type
 * @typeParam maxLength: number - Optional max length
 * @typeParam name: string - Optional name of the input field
 * @typeParam type: InputType - Optional prop with the input type
 * @typeParam inputWrapperClassName: string - Optional prop to apply custom CSS classes to the input wrapper
 * @typeParam disabled: boolean - Optional flag that determines whether the field should be disabled. When enabled, the field will be readonly and uneditable by the user
 * @typeParam allowDecimals: boolean - Optional prop that determines whether decimal values are allowed
 * @typeParam suffix: string - Optional prop to display a suffix next to the input value
 */
export interface ITextInputProps {
    value: string | number;
    onChange?: React.ChangeEventHandler<HTMLInputElement>;
    placeholder?: string;
    inputClassName?: string;
    labelClassName?: string;
    wrapperClassName?: string;
    label?: string;
    error?: boolean;
    isSearch?: boolean;
    maxLength?: number;
    name?: string;
    type?: InputType;
    inputWrapperClassName?: string;
    disabled?: boolean;
    allowDecimals?: boolean;
    suffix?: string;
}

/**
 * Defines the input type options for decimal handling
 */
export const DECIMAL_OPTION = {
    WITH_DECIMALS: 'withDecimals',
    NO_DECIMALS: 'noDecimals',
};

/**
 * Defines invalid characters for numeric input with and without decimals.
 */
export const INVALID_NUMBER_CHARS: IGenericRecord = {
    [DECIMAL_OPTION.WITH_DECIMALS]: ['e', 'E', '+', '-'],
    [DECIMAL_OPTION.NO_DECIMALS]: ['e', 'E', '+', '-', '.'],
};

/**
 * Defines forbidden characters regex for numeric input with and without decimals.
 */
export const FORBIDDEN_NUMERIC_CHARS: IGenericRecord = {
    withDecimals: /[eE+-]/,
    noDecimals: /[eE+-.]/,
};
