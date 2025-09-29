export { FontSizeSelector } from './FontSizeSelector';

/**
 * This describes the properties of each option in the select
 *
 * @typeParam label: string - This is what is shown when you drop down and select an option
 * @typeParam value: string | number - This is the value of each option
 * @typeParam id: string - Optional option id
 */

export interface IOption {
    label: string;
    value: string | number;
    id?: string;
}

/**
 * Interface for the FontSizeSelector component props
 *
 * @typeParam value: string - The current selected value of the input
 * @typeParam options: IOption[] - Array of options to be displayed in the select input
 * @typeParam onChangeOption: (option: ISelectOption, name?: string) => void - Function to be called when an option is selected
 * @typeParam inputClassName: string - Optional prop to apply custom CSS classes to the input element
 * @typeParam labelClassName: string - Optional prop to apply custom CSS classes to the label element
 * @typeParam wrapperClassName: string - Optional prop to apply custom CSS classes to the wrapper element
 * @typeParam containerClassName: string - Optional prop to apply custom CSS classes to the container element
 * @typeParam iconClassName: string - Optional prop to apply custom CSS classes to the icon element
 * @typeParam label: string - Optional label text to be displayed alongside the input
 * @typeParam placeholder: string - Optional placeholder text for the input element
 * @typeParam error: boolean - Optional flag to indicate if the input is in an error state
 * @typeParam name: string - Optional select name
 */
export interface IFontSizeSelectorProps {
    value: string;
    options: IOption[];
    onChangeOption: (option: IOption, name?: string) => void;
    inputClassName?: string;
    labelClassName?: string;
    wrapperClassName?: string;
    containerClassName?: string;
    iconClassName?: string;
    label?: string;
    placeholder?: string;
    error?: boolean;
    name?: string;
}
