import React, { useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { Modal } from '@components/modal';
import { IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { createIndex } from '@redux/executing-index-generation/actions';
import { REQUIRED_FIELDS } from '@constants/Validation';

export const ExecutingIndexGenerationModal: React.FC<{
    toggleModal: () => void;
    toggleToast: () => void;
    handleMessageToast: (message: string) => void;
}> = ({ toggleModal, handleMessageToast, toggleToast }) => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const dispatch = useAppDispatch();

    const [textValue, setTextValue] = useState<string>('');
    const [product, setProduct] = useState<IOption>();
    const [sendModal, setSendModal] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');

    const requireFields = (): boolean => {
        return !product || !textValue;
    };

    const handleSave = async (): Promise<void> => {
        setSendModal(true);
        if (requireFields()) return;

        const response = await dispatch(createIndex({ productId: product?.value, period: textValue }));
                
        if ('error' in response) {
            const jsonString = (response.payload as string).replace(/^Error:\s*/, '');
            const parsed = JSON.parse(jsonString);
            const message = parsed.message;
            setErrorMessage(message);
            return;
        }

        handleMessageToast(response.payload?.message as string);
        toggleToast()
        toggleModal();
    };

    return (
        <Modal open title="Generar archivo de índices" onClose={toggleModal} onSave={handleSave}>
            <div className="flex my-7">
                <SelectSearch
                    label="Producto"
                    wrapperClassName="w-[13.5625rem] mr-[0.6875rem]"
                    value={product?.value as string}
                    options={allProducts as IOption[]}
                    onChangeOption={option => {
                        setErrorMessage('');
                        setProduct(option);
                    }}
                    error={!product && sendModal}
                />
                <TextInput
                    label="Periodo"
                    placeholder={DEFAULT_PLACEHOLDER}
                    wrapperClassName="w-[13.5625rem]"
                    value={textValue}
                    maxLength={8}
                    type="number"
                    onChange={e => {
                        setErrorMessage('');
                        setTextValue(e.target.value);
                    }}
                    error={!textValue && sendModal}
                />
            </div>
            {sendModal && requireFields() && <p className="text-sm text-red-error mb-7">*{REQUIRED_FIELDS}</p>}
            {errorMessage && <p className="w-[25rem] text-sm text-red-error mb-7">*{errorMessage}</p>}
        </Modal>
    );
};
