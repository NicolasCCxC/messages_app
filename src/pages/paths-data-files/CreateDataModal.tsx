import React, { useState } from 'react';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { ReduxResponse } from '@constants/ReduxResponse';
import { Modal } from '@components/modal';
import { IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { ChangeEvent } from '@models/Input';
import { hasEmptyFields } from '@utils/Array';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { DEFAULT_FORM_VALUES, DEFAULT_SELECT_PRODUCT, DEFAULT_SELECT_STATE, MaxLengthField } from '.';
import { IGenericRecord } from '@models/GenericRecord';

export const CreateDataModal: React.FC<{
    toggleModal: () => void;
    toggleToast: () => void;
    handleMessageToast: (message: string) => void;
    products: IOption[];
    createData: (
        formValues: IGenericRecord,
        selectedOptionProduct: IOption,
        selectOptionState: IOption
    ) => Promise<IGenericRecord>;
}> = ({ toggleModal, toggleToast, handleMessageToast, products, createData }) => {
    const [selectedOptionProduct, setSelectedOptionProduct] = useState<IOption>(DEFAULT_SELECT_PRODUCT);
    const [selectOptionState, setSelectOptionState] = useState<IOption>(DEFAULT_SELECT_STATE);
    const [formValues, setFormValues] = useState(DEFAULT_FORM_VALUES);
    const [sendModal, setSendModal] = useState(false);
    const isEmptyFields = hasEmptyFields({ ...formValues, ...selectOptionState, ...selectedOptionProduct });

    const handleTextChange = (e: ChangeEvent): void => {
        const { name, value } = e.target;
        setFormValues(inputValue => ({
            ...inputValue,
            [name]: value,
        }));
    };

    const handleCreateData = async (): Promise<void> => {
        setSendModal(true);
        if (isEmptyFields) return;
        const response = await createData(formValues, selectedOptionProduct, selectOptionState);
        if (response?.meta.requestStatus === ReduxResponse.Fulfilled) {
            setSendModal(false);
            handleMessageToast(response.payload.message);
            toggleToast();
            toggleModal();
        }
    };

    return (
        <Modal open title="Crear" onClose={toggleModal} onSave={handleCreateData} modalClassName="!h-[21.875rem]">
            <div className="w-[445px] my-4.5">
                <SelectSearch
                    onChangeOption={option => setSelectedOptionProduct(option)}
                    value={selectedOptionProduct.label}
                    options={products}
                    label="Producto"
                    placeholder={DEFAULT_PLACEHOLDER}
                    error={sendModal && !selectedOptionProduct.value}
                />
            </div>
            <div className="w-[445px] flex mb-4.5">
                <TextInput
                    name="routeEntry"
                    placeholder={DEFAULT_PLACEHOLDER}
                    value={formValues.routeEntry}
                    onChange={handleTextChange}
                    label="Ruta archivo entrada"
                    inputClassName="h-[1.4375rem]"
                    wrapperClassName="mr-[0.6875rem] w-[13.5625rem]"
                    maxLength={MaxLengthField.Path}
                    error={sendModal && !formValues.routeEntry}
                />
                <TextInput
                    name="routeProcessed"
                    placeholder={DEFAULT_PLACEHOLDER}
                    value={formValues.routeProcessed}
                    onChange={handleTextChange}
                    label="Ruta archivo procesados"
                    inputClassName="h-[1.4375rem]"
                    wrapperClassName="w-[13.5625rem]"
                    maxLength={MaxLengthField.Path}
                    error={sendModal && !formValues.routeProcessed}
                />
            </div>
            <SelectSearch
                onChangeOption={option => setSelectOptionState(option)}
                placeholder={DEFAULT_PLACEHOLDER}
                value={selectOptionState.label}
                options={DEFAULT_STATE_OPTIONS}
                label="Estado"
                wrapperClassName={`w-[13.5625rem] ${sendModal && isEmptyFields ? 'mb-4.5' : 'mb-7'}`}
                error={sendModal && !selectOptionState.value}
            />
            {sendModal && isEmptyFields && <p className="text-sm text-red-error mb-7">*{REQUIRED_FIELDS}</p>}
        </Modal>
    );
};
