export { MultiSelect } from './MultiSelect';

/**
 * Interface for the MultiSelect component props
 *
 * @property options: IOption[] - Array of available options for selection
 * @property handleChangeOption: (option: IOption) => void - Function to handle option selection changes
 * @property selectedOptions: IOption[] - Array of selected options
 * @property label: string - Optional label for input multi select
 * @property wrapperClassName: string - Optional className for wrapper
 * @property inputClassName: string - Optional className for input
 */
export interface IMultiSelectProps {
    options: IOption[];
    handleChangeOption: (option: IOption) => void;
    selectedOptions: IOption[];
    label?: string;
    wrapperClassName?: string;
    inputClassName?: string;
}

/**
 * Interface for the option object
 *
 * @typeParam code: string - Unique identifier for the multi select option
 * @typeParam description: string - Display value or text representation of the select option
 * @typeParam [key: string]: string | boolean - Optional additional custom properties that can be added to the select option
 */
export interface IOption {
    code: string;
    description: string;
    [key: string]: string | boolean;
}
