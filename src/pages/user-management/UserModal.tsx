import { useState } from 'react';
import { Modal } from '@components/modal';
import { IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { ChangeEvent } from '@models/Input';
import { IOption as IOptionMultiselect, MultiSelect } from '@components/multi-select';
import { IGenericRecord } from '@models/GenericRecord';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { DEFAULT_FORM_VALUES, DEFAULT_SELECT_STATE, MAX_LENGHT, options } from '.';
import { hasEmptyFields } from '@utils/Array';
import { createUserManagement } from '@redux/user-management/actions';
import { useAppDispatch } from '@redux/store';
import { NotificationType } from '@components/toast';
import { LETTERS_AND_SPACE_REGEX } from '@constants/Text';

export const UserModal: React.FC<IGenericRecord> = ({ toggleModal, handleNotification }) => {
    const [selectActive, setSelectActive] = useState<IOption>(DEFAULT_SELECT_STATE);
    const [formValues, setFormValues] = useState(DEFAULT_FORM_VALUES);
    const [selectedOptions, setSelectedOptions] = useState<IOptionMultiselect[]>([]);
    const [sendModal, setSendModal] = useState(false);

    const dispatch = useAppDispatch();

    const handleTextChange =
        (validatePattern?: RegExp): ((e: ChangeEvent) => void) =>
        (e: ChangeEvent): void => {
            const { name, value } = e.target;
            if (validatePattern && !validatePattern.test(value)) return;
            setFormValues(inputValue => ({
                ...inputValue,
                [name]: value,
            }));
        };

    const handleSelectActive = (option: IOption): void => setSelectActive(option);

    const toggleOption = (option: IOptionMultiselect): void => {
        setSelectedOptions(prev =>
            prev.some(item => item.description === option.description)
                ? prev.filter(item => item.description !== option.description)
                : [...prev, option]
        );
    };

    const createUser = async (): Promise<void> => {
        setSendModal(true);
        if (hasEmptyFields(formValues) || selectedOptions.length === 0) return;
        const response = await dispatch(
            createUserManagement({
                ...formValues,
                active: !!selectActive.value,
                roleCodes: selectedOptions.map(option => parseInt(option.code)),
            })
        );

        if ('error' in response) {
            const jsonString = (response.payload as string).replace(/^Error:\s*/, '');
            const parsed = JSON.parse(jsonString);
            const message = parsed.message;
            handleNotification(message, NotificationType.Error);
            return;
        }

        setFormValues(DEFAULT_FORM_VALUES);
        setSelectActive(DEFAULT_SELECT_STATE);
        setSelectedOptions([]);

        // @ts-expect-error Property 'message' does not exist on type 'unknown'.
        handleNotification(response.message, undefined);
        toggleModal();
    };

    return (
        <Modal open title="Crear" onClose={toggleModal} onSave={createUser}>
            <form className="w-[27.8125rem] my-7">
                <fieldset className="flex justify-between">
                    <TextInput
                        name="email"
                        value={formValues.email}
                        onChange={handleTextChange()}
                        label="Usuario de red"
                        inputClassName="h-[1.5625rem] "
                        wrapperClassName="w-[13.5625rem]"
                        maxLength={MAX_LENGHT.email}
                    />
                    <TextInput
                        name="name"
                        value={formValues.name}
                        onChange={handleTextChange(LETTERS_AND_SPACE_REGEX)}
                        label="Nombres y apellidos"
                        inputClassName="h-[1.5625rem] "
                        wrapperClassName="w-[13.5625rem]"
                        maxLength={MAX_LENGHT.name}
                    />
                </fieldset>
                <fieldset className="flex justify-between my-4.5">
                    <MultiSelect
                        wrapperClassName="w-[13.5625rem]"
                        inputClassName="border min-h-[1.5625rem] border-gray-dark px-2.5"
                        label="Rol o roles"
                        options={options}
                        handleChangeOption={toggleOption}
                        selectedOptions={selectedOptions}
                    />

                    <SelectSearch
                        onChangeOption={handleSelectActive}
                        value={selectActive.label}
                        options={DEFAULT_STATE_OPTIONS}
                        label="Estado"
                        wrapperClassName="w-[13.5625rem]"
                    />
                </fieldset>
                {sendModal && (hasEmptyFields(formValues) || selectedOptions.length === 0) && (
                    <p className="text-sm text-red-error mt-4.5">*Campos obligatorios</p>
                )}
            </form>
        </Modal>
    );
};
