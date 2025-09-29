import { useState } from 'react';
import { Modal } from '@components/modal';
import { type IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { REQUIRED_FIELDS as REQUIRED_FIELDS_MESSAGE } from '@constants/Validation';
import type { ChangeEvent } from '@models/Input';
import type { IGenericRecord } from '@models/GenericRecord';
import type { ICreateRecordModalProps } from '@models/CreateRecordModal';
import { createInput } from '@redux/product-input/actions';
import { useAppDispatch } from '@redux/store';
import { hasEmptyFields } from '@utils/Object';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { INPUT_PROPS, DEFAULT_INPUT, REQUIRED_FIELDS, FieldName as Field, defaultSelectTypeOptions } from '.';

export const CreateRecordModal: React.FC<ICreateRecordModalProps> = ({ products, toggleModal, updateNotification }) => {
    const dispatch = useAppDispatch();

    const [data, setData] = useState(DEFAULT_INPUT);
    const [validate, setValidate] = useState(false);
    const [validationMessage, setValidationMessage] = useState('');
    const [selectTypeLabel, setSelectTypeLabel] = useState('');

    const handleTextChange = ({ target: { name, value } }: ChangeEvent): void => {
        setData(prev => ({ ...prev, [name]: value }));
    };

    const handleSelectActive = (option: IOption): void => {
        setData({ ...data, productId: option.value as string, option: option.label });
    };

    const handleSelectType = (option: IOption): void => {
        setSelectTypeLabel(option.label);
        setData({ ...data, type: option.value as string });
    };

    const hasValidationErrors = (): boolean => {
        const hasErrors = hasEmptyFields(data, REQUIRED_FIELDS);
        if (hasErrors) {
            setValidationMessage(REQUIRED_FIELDS_MESSAGE);
            setValidate(true);
        }
        return hasErrors;
    };

    const handleResponse = (payload?: IGenericRecord['payload']): void => {
        if (!payload?.data) {
            setValidationMessage(payload?.message);
            return;
        }
        updateNotification(payload.message);
        toggleModal();
    };

    const submitData = async (): Promise<IGenericRecord> => {
        const { payload }: IGenericRecord = await dispatch(createInput(data));
        return payload;
    };

    const handleSubmit = async (): Promise<void> => {
        if (hasValidationErrors()) return;
        const payload = await submitData();
        handleResponse(payload);
    };

    const { Product, FieldName, RegistrationIdentifier, RegistrationName, InitialPosition, EndPosition, IndexFileIdentifier } =
        Field;

    return (
        <Modal open title="Crear" onClose={toggleModal} onSave={handleSubmit}>
            <form className="w-[27.8125rem] my-7">
                <SelectSearch
                    {...INPUT_PROPS[Product]}
                    error={validate && !data.option}
                    options={products as IOption[]}
                    onChangeOption={handleSelectActive}
                    value={data.option}
                />
                <fieldset className="flex justify-between mt-4.5">
                    <TextInput
                        {...INPUT_PROPS[FieldName]}
                        error={validate && !data[FieldName]}
                        onChange={handleTextChange}
                        value={data[FieldName]}
                    />
                    <TextInput
                        {...INPUT_PROPS[RegistrationIdentifier]}
                        error={validate && !data[RegistrationIdentifier]}
                        onChange={handleTextChange}
                        value={data?.[RegistrationIdentifier]}
                    />
                </fieldset>
                <fieldset className="flex justify-between my-4.5">
                    <TextInput
                        {...INPUT_PROPS[RegistrationName]}
                        error={validate && !data[RegistrationName]}
                        value={data[RegistrationName]}
                        onChange={handleTextChange}
                    />
                    <TextInput
                        {...INPUT_PROPS[InitialPosition]}
                        error={validate && !data[InitialPosition]}
                        onChange={handleTextChange}
                        value={data[InitialPosition]}
                    />
                </fieldset>
                <fieldset className="flex justify-between h-[3.8125rem]">
                    <TextInput
                        {...INPUT_PROPS[EndPosition]}
                        error={validate && !data[EndPosition]}
                        value={data[EndPosition]}
                        onChange={handleTextChange}
                    />
                    <TextInput
                        {...INPUT_PROPS[IndexFileIdentifier]}
                        value={data[IndexFileIdentifier]}
                        onChange={handleTextChange}
                        wrapperClassName="self-end h-full w-[13.5625rem]"
                    />
                </fieldset>
                <fieldset className="flex w-full mt-4.5">
                    <SelectSearch
                        label="Tipo"
                        placeholder={DEFAULT_PLACEHOLDER}
                        wrapperClassName="w-full"
                        error={validate && !data.option}
                        options={defaultSelectTypeOptions}
                        onChangeOption={handleSelectType}
                        value={selectTypeLabel}
                    />
                </fieldset>
                {validationMessage && <p className="text-sm text-red mt-4.5">*{validationMessage}</p>}
            </form>
        </Modal>
    );
};
