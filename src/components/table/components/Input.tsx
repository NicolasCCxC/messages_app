import { forwardRef, useEffect, useState } from 'react';
import { Autocomplete, Popper, PopperProps, TextField } from '@mui/material';
import arrow from '@assets/icons/arrow-down.svg';
import { IGenericRecord } from '@models/GenericRecord';
import { DEFAULT_SELECT } from '@pages/manage-content-product';
import { Icon } from '@components/icon';
import { useOutsideClick } from '@hooks/useOutsideClick';
import { IFieldProps } from '.';
import './Styles.scss';

export const Input: React.FC<IFieldProps> = ({ handleChange, isEditable, item }) => {
    const { maxLength, name, type = 'text', isCustom } = item;

    return (
        <>
            {isCustom ? (
                <div className="text-gray-dark" dangerouslySetInnerHTML={{ __html: item?.[name] }} />
            ) : (
                <input
                    className={`w-full bg-transparent ${isEditable ? 'text-black' : 'text-gray-dark'} text-sm`}
                    disabled={!isEditable}
                    type={type}
                    value={item?.[name] ?? ''}
                    onChange={({ target: { value } }) => handleChange(value)}
                    maxLength={maxLength}
                />
            )}
        </>
    );
};

const CustomPopper: React.FC<PopperProps> = props => {
    return <Popper {...props} className="autocomplete-popper" />;
};

export const Select: React.FC<IFieldProps> = ({ handleChange, isEditable, item }) => {
    const value = item?.[item?.name];
    const { availableOptions, options = [] } = item;
    const [selectedOption, setSelectedOption] = useState(options.find(option => option?.value === String(value)));
    const [open, setOpen] = useState(false);

    useEffect(() => {
        setSelectedOption(options.find(option => option?.value === String(value)));
    }, [value, options]);

    return (
        <Autocomplete
            options={availableOptions ?? options}
            slots={{ popper: CustomPopper }}
            getOptionLabel={option => option.label}
            open={open}
            disableClearable
            onOpen={() => setOpen(true)}
            onClose={() => setOpen(false)}
            clearOnEscape
            renderOption={(_, option, { index }) => {
                return (
                    <div key={option.value} className="cursor-pointer hover:bg-blue-light px-1.5">
                        <p
                            className={`text-sm font-normal font-arial h-9 flex items-center hover:text-white ${
                                options.length === index + 1 ? '' : 'border-b border-[#000]'
                            } ${selectedOption?.label === option.label ? 'text-black' : 'text-gray-dark'}`}
                            onClick={() => {
                                handleChange(option?.value);
                                setOpen(false);
                            }}
                        >
                            {option.label}
                        </p>
                    </div>
                );
            }}
            sx={{ border: 'none', borderRadius: 0, fontSize: 10, '.MuiOutlinedInput-notchedOutline': { border: 'none' } }}
            renderInput={params => (
                <TextField {...params} value={selectedOption?.label} variant="standard" className="autocomplete-input" placeholder="Selecciona una opción" />
            )}
            disabled={!isEditable}
            value={selectedOption ?? DEFAULT_SELECT}
            popupIcon={<SelectArrow />}
        />
    );
};

const SelectArrow: React.FC = forwardRef<HTMLSpanElement>((props, ref) => (
    <span {...props} ref={ref}>
        <img alt="Arrow" className="select-arrow" src={arrow} />
    </span>
));

export const MultiSelect: React.FC<IFieldProps> = ({ handleChange, isEditable, item }) => {
    const [isOpen, setIsOpen] = useState(false);
    const selectInputRef = useOutsideClick(() => setIsOpen(false));
    const selectedOptions = item[item.name] || [];
    const options = item.multiSelectOptions || [];

    const toggleOption = (option: IGenericRecord): void => {
        const newSelectedOptions = selectedOptions.some(
            (selectedOption: IGenericRecord) => selectedOption.description === option.description
        )
            ? selectedOptions.filter((selectedOption: IGenericRecord) => selectedOption.description !== option.description)
            : [...selectedOptions, option];

        handleChange(newSelectedOptions);
    };

    return (
        <div className="relative" ref={selectInputRef}>
            <div
                role="button"
                tabIndex={0}
                className={`flex justify-between bg-transparent cursor-pointer ${isEditable ? 'text-black' : 'text-gray-dark'}`}
                onClick={() => isEditable && setIsOpen(!isOpen)}
                onKeyDown={() => isEditable && setIsOpen(!isOpen)}
            >
                <p className="max-w-[9.375rem] overflow-hidden text-nowrap overflow-ellipsis">
                    {selectedOptions.length > 0
                        ? selectedOptions?.map((option: IGenericRecord) => option.description).join(', ')
                        : 'Seleccionar'}
                </p>
                {isEditable && <Icon name="arrowDown" className={`ml-2 transition-transform ${isOpen ? 'rotate-180' : ''}`} />}
            </div>
            {isOpen && isEditable && (
                <div className="absolute left-0 right-0 z-10 w-full mt-1 bg-[#ECF0F1] rounded shadow-lg">
                    {options?.map((option: IGenericRecord, index: number) => {
                        const { description } = option;
                        return (
                            <label
                                key={description}
                                className={`${
                                    selectedOptions.map((option: IGenericRecord) => option.description).includes(description)
                                        ? 'text-black'
                                        : 'text-gray-dark'
                                } ${
                                    options.length > index + 1 ? 'border-b border-[#000000]' : ''
                                } flex items-center gap-2 py-1 mx-2 text-sm hover:bg-gray-100`}
                            >
                                <span
                                    className={`w-4 h-4 border border-[#000000] ${
                                        selectedOptions.map((option: IGenericRecord) => option.description).includes(description)
                                            ? 'bg-blue-light'
                                            : 'bg-gray'
                                    }`}
                                />
                                <input
                                    type="checkbox"
                                    checked={selectedOptions
                                        .map((option: IGenericRecord) => option.description)
                                        .includes(description)}
                                    onChange={() => {
                                        toggleOption(option);
                                    }}
                                    className="hidden"
                                />
                                {description}
                            </label>
                        );
                    })}
                </div>
            )}
        </div>
    );
};
