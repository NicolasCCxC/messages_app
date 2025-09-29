import { useState } from 'react';
import { useAppDispatch } from '@redux/store';
import { createProductManagement } from '@redux/product-management/actions';
import { hasEmptyFields } from '@utils/Array';
import { IOption, SelectSearch } from '@components/select-search';
import { ChangeEvent } from '@models/Input';
import { ReduxResponse } from '@constants/ReduxResponse';
import { Modal } from '@components/modal';
import { TextInput } from '@components/text-input';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { ALPHA_NUMERIC_REGEX } from '@constants/Text';
import { validatePattern } from '@utils/Input';
import { DEFAULT_FORM_VALUES, DEFAULT_SELECT_STATE, MaxLengthField } from '.';

export const CreateProductModal: React.FC<{
    toggleModal: () => void;
    toggleToast: () => void;
    handleMessageToast: (message: string) => void;
}> = ({ toggleModal, toggleToast, handleMessageToast }) => {
    const dispatch = useAppDispatch();

    const [sendModal, setSendModal] = useState(false);
    const [selectOption, setSelectOption] = useState<IOption>(DEFAULT_SELECT_STATE);
    const [formValues, setFormValues] = useState(DEFAULT_FORM_VALUES);
    const [errorMessage, setErrorMessage] = useState('');

    const handleSelectChange = (option: IOption): void => setSelectOption(option);

    const handleTextChange = (e: ChangeEvent): void => {
        setErrorMessage('');
        const { name, value } = e.target;
        if (name === 'code' && !validatePattern(value, ALPHA_NUMERIC_REGEX)) return;
        setFormValues(inputValue => ({
            ...inputValue,
            [name]: value,
        }));
    };

    const createProduct = async (): Promise<void> => {
        setSendModal(true);
        if (hasEmptyFields(formValues) || !selectOption.value) return;
        const response = await dispatch(
            createProductManagement({
                ...formValues,
                active: JSON.parse(selectOption.value as string),
            })
        );

        if ('error' in response) {
            const jsonString = (response.payload as string).replace(/^Error:\s*/, '');
            const parsed = JSON.parse(jsonString);
            setErrorMessage(parsed.message);
            return;
        }

        if (response.meta.requestStatus === ReduxResponse.Fulfilled) {
            setErrorMessage('');
            setSendModal(false);
            handleMessageToast(response.payload.message);
            setFormValues(DEFAULT_FORM_VALUES);
            setSelectOption(DEFAULT_SELECT_STATE);
            toggleToast();
            toggleModal();
        }
    };

    return (
        <Modal
            modalClassName={errorMessage || (sendModal && hasEmptyFields(formValues) ? '' : 'h-[21.8125rem]')}
            open
            title="Crear"
            onClose={toggleModal}
            onSave={createProduct}
        >
            <div className="w-[27.8125rem] my-7">
                <div className="flex mb-4.5">
                    <TextInput
                        name="code"
                        value={formValues.code}
                        onChange={handleTextChange}
                        wrapperClassName="w-[13.5625rem] mr-[0.6875rem]"
                        inputClassName="h-[1.4375rem]"
                        label="Código del producto"
                        maxLength={MaxLengthField.Code}
                        error={sendModal && !formValues.code}
                    />
                    <SelectSearch
                        onChangeOption={handleSelectChange}
                        value={selectOption.label}
                        options={DEFAULT_STATE_OPTIONS}
                        label="Estado del producto"
                        wrapperClassName="w-[13.5625rem]"
                        error={sendModal && !selectOption.value}
                    />
                </div>

                <TextInput
                    name="description"
                    value={formValues.description}
                    onChange={handleTextChange}
                    label="Descripción del producto"
                    inputClassName="h-[1.4375rem]"
                    wrapperClassName="mb-4.5"
                    maxLength={MaxLengthField.Description}
                    error={sendModal && !formValues.description}
                />
                <TextInput
                    name="documentType"
                    value={formValues.documentType}
                    onChange={handleTextChange}
                    label="DocumentTYpe"
                    inputClassName="h-[1.4375rem]"
                    maxLength={MaxLengthField.Description}
                    error={sendModal && !formValues.documentType}
                />
                {sendModal && hasEmptyFields(formValues) && <p className="text-sm text-red-error mt-4.5">*{REQUIRED_FIELDS}</p>}
                {errorMessage && <p className="text-sm text-red-error mt-4.5">*{errorMessage}</p>}
            </div>
        </Modal>
    );
};
