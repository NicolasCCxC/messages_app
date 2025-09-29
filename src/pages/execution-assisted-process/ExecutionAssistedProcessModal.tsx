import React, { useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { createAssistedProcess } from '@redux/execution-assisted-process/actions';
import { urls } from '@api/Urls';
import { FetchRequest } from '@models/Request';
import { Modal } from '@components/modal';
import { IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { apiGetFormat } from '@api/ExecutionAssistedProcess';

export const ExecutionAssistedProcessModal: React.FC<{
    toggleModal: () => void;
    toggleToast: () => void;
    handleMessageToast: (message: string) => void;
}> = ({ toggleModal, toggleToast, handleMessageToast }) => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const dispatch = useAppDispatch();

    const [sendModal, setSendModal] = useState(false);
    const [textValues, setTextValues] = useState({ format: '', period: '' });
    const [product, setProduct] = useState<string>('');
    const [errorMessage, setErrorMessage] = useState('');

    const getFormat = async (productId: string): Promise<void> => {
        const request = new FetchRequest(urls.executingAssistedProcess.getFormat(productId));
        /* eslint-disable @typescript-eslint/no-explicit-any */
        const { data }: any = await apiGetFormat(request);
        if (data.version) {
            setTextValues(prev => ({ ...prev, format: data.version }));
        }
    };

    const handleTextValue = (name: string, value: string): void => {
        setErrorMessage('');
        setTextValues(prev => ({ ...prev, [name]: value }));
    };

    const hasEmptyFields = (): boolean => {
        return !product || !textValues.period;
    };

    const handleSelectProduct = (option: IOption): void => {
        setErrorMessage('');
        setProduct(option.value as string);
        getFormat(option.id as string);
    };

    const handleSave = async (): Promise<void> => {
        setSendModal(true);
        if (hasEmptyFields()) return;

        const response = await dispatch(createAssistedProcess({ productId: product, period: textValues.period }));

        if ('error' in response) {
            const jsonString = (response.payload as string).replace(/^Error:\s*/, '');
            const parsed = JSON.parse(jsonString);
            const message = parsed.message;
            setErrorMessage(message);
            return;
        }

        handleMessageToast(response.payload?.message as string);
        toggleToast();
        toggleModal();
    };

    return (
        <Modal open title="Generar extractos" onClose={toggleModal} onSave={handleSave}>
            <div className="flex mt-7 mb-[0.6875rem]">
                <SelectSearch
                    label="Producto"
                    wrapperClassName="w-[13.5625rem] mr-[0.6875rem]"
                    value={product}
                    options={allProducts as IOption[]}
                    onChangeOption={handleSelectProduct}
                    error={sendModal && !product}
                />
                <TextInput
                    label="Formato"
                    name="format"
                    placeholder={DEFAULT_PLACEHOLDER}
                    wrapperClassName="w-[13.5625rem]"
                    value={textValues.format}
                    maxLength={8}
                    type="number"
                    onChange={e => handleTextValue(e.target.name, e.target.value)}
                    disabled
                    error={sendModal && !textValues.format && !!product}
                />
            </div>
            <div className="flex mb-7">
                <TextInput
                    label="Periodo"
                    name="period"
                    placeholder={DEFAULT_PLACEHOLDER}
                    wrapperClassName="w-full mr-[0.6875rem]"
                    value={textValues.period}
                    maxLength={8}
                    type="number"
                    onChange={e => handleTextValue(e.target.name, e.target.value)}
                    error={sendModal && !textValues.period}
                />
            </div>
            {sendModal && hasEmptyFields() && <p className="text-sm text-red-error mb-4.5">*{REQUIRED_FIELDS}</p>}
            {errorMessage && <p className="w-[25rem] text-sm text-red-error mb-7">*{errorMessage}</p>}
        </Modal>
    );
};
