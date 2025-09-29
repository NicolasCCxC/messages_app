import { useState } from 'react';
import { Modal } from '@components/modal';
import { type IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { REQUIRED_FIELDS } from '@constants/Validation';
import type { ChangeEvent } from '@models/Input';
import type { IGenericRecord } from '@models/GenericRecord';
import type { ICreateRecordModalProps } from '@models/CreateRecordModal';
import { createPath } from '@redux/paths/actions';
import { useAppDispatch } from '@redux/store';
import { hasEmptyFields } from '@utils/Object';
import { DEFAULT_FORM_VALUES, MAX_OUTBOUND_PATH_LENGTH } from '.';

export const CreateRecordModal: React.FC<ICreateRecordModalProps> = ({ products, toggleModal, updateNotification }) => {
    const dispatch = useAppDispatch();

    const [data, setData] = useState(DEFAULT_FORM_VALUES);
    const [validate, setValidate] = useState(false);

    const handleTextChange = ({ target: { name, value } }: ChangeEvent): void => {
        setData(prev => ({ ...prev, [name]: value }));
    };

    const handleSelectActive = (option: IOption): void => {
        setData({ ...data, productId: option.value as string, option: option.label });
    };

    const handleSubmit = async (): Promise<void> => {
        if (hasEmptyFields(data)) return setValidate(true);
        const { payload }: IGenericRecord = await dispatch(createPath(data));
        updateNotification(payload?.message);
        toggleModal();
    };

    return (
        <Modal open title="Crear" onClose={toggleModal} onSave={handleSubmit}>
            <form className="w-[27.8125rem] my-7">
                <SelectSearch
                    onChangeOption={handleSelectActive}
                    value={data.option}
                    options={products as IOption[]}
                    label="Producto"
                    wrapperClassName="w-full"
                />
                <fieldset className="flex justify-between mt-4.5">
                    <TextInput
                        name="routeOutputExtract"
                        value={data.routeOutputExtract}
                        onChange={handleTextChange}
                        label="Ruta salida extracto"
                        inputClassName="h-[1.5625rem]"
                        wrapperClassName="w-[13.5625rem] h-[2.8125rem]"
                        maxLength={MAX_OUTBOUND_PATH_LENGTH}
                    />
                    <TextInput
                        name="routeOutputIndex"
                        value={data.routeOutputIndex}
                        onChange={handleTextChange}
                        label="Ruta salida archivo de índice"
                        inputClassName="h-[1.5625rem]"
                        wrapperClassName="w-[13.5625rem] h-[2.8125rem]"
                        maxLength={MAX_OUTBOUND_PATH_LENGTH}
                    />
                </fieldset>
                {validate && <p className="text-sm text-red mt-4.5">*{REQUIRED_FIELDS}</p>}
            </form>
        </Modal>
    );
};
