import { useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import type { IGenericRecord } from '@models/GenericRecord';
import { getAllInputs } from '@redux/product-input/actions';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { Modal } from '@components/modal';
import { TextInput } from '@components/text-input';
import type { ChangeEvent } from '@models/Input';
import { type IOption, SelectSearch } from '@components/select-search';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { createManageContentProduct, modifyManageContentProduct } from '@redux/manage-content-product/actions';
import { ReduxResponse } from '@constants/ReduxResponse';
import { RequiredFields } from './RequiredFields';
import { DEFAULT_SELECT, IFieldState, FILE_OPTIONS, ICreateDataModal, MAX_LENGHT_FIELD, DEFAULT_REQUIRED_VALUES } from '.';

export const CreateDataModal: React.FC<ICreateDataModal> = ({
    toggleModal,
    products,
    toggleToast,
    handleMessageToast,
    modifyData,
    isModify,
    handleUpdateData,
}) => {
    const { allInputs } = useAppSelector(state => state.productInput);
    const { content } = useAppSelector(state => state.manageContentProduct);
    const initialProductOption: IOption = isModify
        ? products.find(product => product.value === modifyData.product)!
        : DEFAULT_SELECT;

    const initialFileOption: IOption = isModify ? { value: modifyData.typeFile, label: modifyData.typeFile } : DEFAULT_SELECT;

    const initialNameIndexFile: string = isModify ? modifyData.nameIndexFile : '';

    const formattedRequiredFields = useMemo(() => {
        return modifyData?.requiredFields?.map((item: IGenericRecord) => ({
            id: item.id,
            isFixed: item.isFixed,
            content: item.content,
            inputProductStructureId: item.content ? null : item.inputStructureProduct?.id ?? null,
        }));
    }, [modifyData.requiredFields]);

    const initialFields: IFieldState[] = isModify ? formattedRequiredFields : [DEFAULT_REQUIRED_VALUES];

    const [selectedOptionProduct, setSelectedOptionProduct] = useState<IOption>(initialProductOption);
    const [selectedOptionFile, setSelectedOptionFile] = useState<IOption>(initialFileOption);
    const [nameIndexFile, setNameIndexFile] = useState<string>(initialNameIndexFile);
    const [fields, setFields] = useState<IFieldState[]>(initialFields);
    const [sendModal, setSendModal] = useState(false);
    const dispatch = useAppDispatch();

    useEffect(() => {
        if (isModify) {
            dispatch(getAllInputs(modifyData.product));
        }
    }, [dispatch, isModify, modifyData.product]);

    const hasEmptyRequiredFields = (): boolean => {
        if (!selectedOptionProduct.value || !selectedOptionFile.value || !nameIndexFile) {
            return true;
        }

        return fields.some(field => {
            if (field.isFixed && !field.content) return true;
            if (!field.isFixed && !field.inputProductStructureId) return true;
            return false;
        });
    };

    const handleTextChange = (e: ChangeEvent): void => {
        const { value } = e.target;
        setNameIndexFile(value);
    };

    const inputOptions = useMemo(
        () => allInputs.map((input: IGenericRecord) => ({ value: input.id, label: input.fieldName })),
        [allInputs]
    );

    const handleSelectProduct = (option: IOption): void => {
        setSelectedOptionProduct(option);
        setFields([DEFAULT_REQUIRED_VALUES]);
        dispatch(getAllInputs(option.value as string));
    };

    const updateField = (index: number, updatedValues: Partial<IFieldState>): void => {
        setFields(prevFields => prevFields.map((field, i) => (i === index ? { ...field, ...updatedValues } : field)));
    };

    const handleAddField = (): void => {
        setFields([...fields, { isFixed: false, content: null, inputProductStructureId: null }]);
    };

    const createData = async (): Promise<void> => {
        setSendModal(true);
        if (hasEmptyRequiredFields()) return;

        const formData: IGenericRecord = {
            productId: selectedOptionProduct.value,
            typeFile: selectedOptionFile.value,
            nameIndexFile: nameIndexFile,
            requiredFields: fields,
        };

        let response;
        if (isModify) {
            const contToCompare = content.find((cont: IGenericRecord) => cont.id === modifyData.id) ?? {};
            if (contToCompare.product === formData.productId) delete formData.productId;
            response = await dispatch(modifyManageContentProduct({ formData, id: modifyData.id }));
            handleUpdateData((response as IGenericRecord).payload.content);
        } else response = await dispatch(createManageContentProduct(formData));

        if (response.meta.requestStatus === ReduxResponse.Fulfilled) {
            setSendModal(false);
            // @ts-expect-error Property 'message' does not exist on type 'unknown'.
            handleMessageToast(response.payload.message);
            toggleToast();
            toggleModal();
        }
    };

    const handleOptions = products.filter(product => {
        const haveCont = content.find((cont: IGenericRecord) => product.value === cont.product);
        return !haveCont;
    });

    return (
        <Modal
            open
            title={isModify ? 'Modificar' : 'Crear'}
            onClose={toggleModal}
            onSave={createData}
            saveButtonText={isModify ? 'Modificar' : 'Crear'}
            modalClassName={selectedOptionProduct.value ? '!min-h-[23.25rem]' : ''}
        >
            <div className="w-[27.8125rem] my-4.5">
                <SelectSearch
                    onChangeOption={option => handleSelectProduct(option)}
                    value={selectedOptionProduct.label || ''}
                    options={handleOptions}
                    label="Producto"
                    placeholder={DEFAULT_PLACEHOLDER}
                    error={sendModal && !selectedOptionProduct.value}
                />
            </div>
            <div className="w-[27.8125rem] flex mb-4.5">
                <SelectSearch
                    value={selectedOptionFile.label}
                    placeholder={DEFAULT_PLACEHOLDER}
                    onChangeOption={option => setSelectedOptionFile(option)}
                    options={FILE_OPTIONS}
                    label="Tipo de archivo"
                    inputClassName="h-[1.4375rem]"
                    wrapperClassName="mr-[0.6875rem] w-[13.5625rem]"
                    error={sendModal && !selectedOptionFile.value}
                />
                <TextInput
                    name="nameIndexFile"
                    placeholder={DEFAULT_PLACEHOLDER}
                    value={nameIndexFile}
                    onChange={handleTextChange}
                    label="Nombre archivo de índice"
                    inputClassName="h-[1.4375rem]"
                    wrapperClassName="w-[13.5625rem]"
                    maxLength={MAX_LENGHT_FIELD.nameIndexFile}
                    error={sendModal && !nameIndexFile}
                />
            </div>
            <RequiredFields
                sendModal={sendModal}
                fields={fields}
                updateField={updateField}
                onAddField={handleAddField}
                options={selectedOptionProduct.value ? inputOptions : []}
            />
            {sendModal && hasEmptyRequiredFields() && <p className="text-sm text-red-error mb-7">*{REQUIRED_FIELDS}</p>}
        </Modal>
    );
};
