import { useContext, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getAllProducts } from '@redux/product-management/actions';
import { createObjectManageFormat, modifyObjectManageFormat } from '@redux/object-manage-format/actions';
import { IGenericRecord } from '@models/GenericRecord';
import { ChangeEvent } from '@models/Input';
import { ElementType, ObjectType, PLACEHOLDERS } from '@constants/ObjectsEditor';
import { Icon } from '@components/icon';
import { IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { Button } from '@components/button';
import { DEFAULT_PLACEHOLDER } from '@constants/DefaultPlaceholder';
import { NotificationType, Toast } from '@components/toast';
import { DialogModal, DialogModalType } from '@components/modal';
import { ENTER } from '@components/form';
import { ELEMENT_TYPE_TO_OBJECT_TYPE_MAP, IElement, ManageObjectContext } from '@pages/object-manage-format/context';
import { getDiff } from '@utils/Diff';
import { Sidebar } from './Sidebar';
import { SidebarTools } from '../tools';
import { elements, IEditorProps } from '..';
import { maxLengthText, maxLengthFields } from '.';

export const Editor: React.FC<IEditorProps> = ({
    toggleEditor,
    elementToModify,
    toggleModify,
    toggleToast,
    setSaveError,
    handleMessageToast,
    isModify,
}) => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const { element, selectedElementType, updateElementProperties, setElement, setSelectedElementType } =
        useContext(ManageObjectContext);
    const [selectProductValue, setSelectProductValue] = useState('');
    const [isDataSend, setIsDataSend] = useState(false);
    const [showReturnModal, setShowReturnModal] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const isSelectTable = selectedElementType === ElementType.Table;
    const requiredFieldsEmpty = !element.name || !element.identifier || !element.productId;

    const dispatch = useAppDispatch();

    useEffect(() => {
        dispatch(getAllProducts());
        return (): void => {
            setElement({} as IElement);
            setSelectedElementType(null);
        };
    }, [dispatch, setElement, setSelectedElementType]);

    useEffect(() => {
        if (isModify && elementToModify) {
            setElement({
                ...(elementToModify as IElement),
                objectType: ELEMENT_TYPE_TO_OBJECT_TYPE_MAP[elementToModify.type] || ObjectType.Generic,
            });
            setSaveError(false);
            setSelectedElementType(elementToModify.type);
            const product = allProducts.find((product: IGenericRecord) => product.id === elementToModify.productId);
            setSelectProductValue(product?.label);
        }
    }, [setElement, isModify, elementToModify, allProducts, setSelectedElementType]);

    const ElementToToShow = useMemo(() => selectedElementType && elements[selectedElementType], [selectedElementType]);

    const handleSelectProduct = (option: IOption): void => {
        updateElementProperties('productId', option.value);
        setSelectProductValue(option.label);
    };

    const handleObjectCode = (e: ChangeEvent): void => updateElementProperties('identifier', e.target.value);

    const handleObjectName = (e: ChangeEvent): void => updateElementProperties('name', e.target.value);

    const handleResponseError = (res: IGenericRecord): boolean => {
        if ('error' in res) {
            const jsonString = (res.payload as string).replace(/^Error:\s*/, '');
            const parsed = JSON.parse(jsonString);
            const message = parsed.message;
            setErrorMessage(message);
            return true;
        }
        return false;
    };

    const createObject = async (): Promise<void> => {
        if (requiredFieldsEmpty) {
            setIsDataSend(true);
            return;
        }
        if (isModify) {
            const elementWithoutDimensions = element.type === ElementType.Text ? removeElementDimensions(element) : element;
            const diff = getDiff<IGenericRecord>(elementToModify, elementWithoutDimensions);
            const { id } = element;
            const res: IGenericRecord = await dispatch(modifyObjectManageFormat({ diff, id }));
            if (handleResponseError(res)) return;
            toggleModify();
            toggleEditor();
            handleMessageToast(res.payload.message);
            toggleToast();
            setErrorMessage('');
            return;
        }

        const elementToCreate = element.type === ElementType.Text ? removeElementDimensions(element) : element;
        const res: IGenericRecord = await dispatch(createObjectManageFormat(elementToCreate));
        if (handleResponseError(res)) return;
        setSaveError(false);
        toggleEditor();
        setErrorMessage('');
        handleMessageToast(res.payload.message);
        toggleToast();
    };

    const removeElementDimensions = (item: IGenericRecord): IGenericRecord => {
        const modifiedItem = { ...item, style: { ...item.style } };
        if (item?.content?.length < maxLengthText) {
            delete modifiedItem.style.width;
            delete modifiedItem.style.height;
        }
        return modifiedItem;
    };

    const handleLeaveEditor = (): void => {
        if (isModify) {
            toggleModify();
            toggleEditor();
            return;
        }

        if (element.type) {
            setShowReturnModal(true);
            return;
        }

        toggleEditor();
    };

    const handleReturnModal = (): void => {
        toggleEditor();
        if (isModify) toggleModify();
    };

    return (
        <>
            <div className="flex mb-4.5 pl-[2.375rem]">
                <span
                    tabIndex={0}
                    role="button"
                    className="w-[2.625rem] flex justify-center items-center rounded-lg h-[1.75rem] cursor-pointer bg-white"
                    onClick={handleLeaveEditor}
                    onKeyDown={e => e.key === ENTER && handleLeaveEditor()}
                >
                    <Icon name="arrowLeftBlueRounded" className="w-[1.375rem] h-[1.375rem]" />
                </span>
                <p className="ml-2 text-lg text-black">Propiedades del objeto</p>
            </div>
            <div className="flex mb-4.5 pl-[2.375rem]">
                <SelectSearch
                    wrapperClassName="w-[13.5625rem] mr-4.5"
                    placeholder={PLACEHOLDERS.select}
                    label="Producto"
                    value={element.productId ? selectProductValue : ''}
                    onChangeOption={handleSelectProduct}
                    options={allProducts as IOption[]}
                    error={isDataSend && !element.productId}
                />
                <TextInput
                    wrapperClassName="w-[13.5625rem] mr-[0.9375rem]"
                    inputClassName="h-[1.4375rem]"
                    label="Código de objeto"
                    placeholder={DEFAULT_PLACEHOLDER}
                    value={element.identifier}
                    onChange={handleObjectCode}
                    maxLength={maxLengthFields.identifier}
                    error={isDataSend && !element.identifier}
                />

                <TextInput
                    wrapperClassName="w-[13.5625rem]"
                    inputClassName="h-[1.4375rem]"
                    label="Nombre del objeto"
                    placeholder={DEFAULT_PLACEHOLDER}
                    value={element.name}
                    onChange={handleObjectName}
                    maxLength={maxLengthFields.name}
                    error={isDataSend && !element.name}
                />
            </div>
            <div className="flex">
                {!isModify && <Sidebar />}

                <div
                    role="button"
                    tabIndex={0}
                    onDragOver={e => e.preventDefault()}
                    className={`${
                        isSelectTable || isModify ? 'w-full' : 'w-[42.3125rem]'
                    } h-[29.8125rem] border border-gray-dark bg-white px-[1.125rem] overflow-y-auto relative`}
                >
                    <p className="text-base leading-5 mb-[1.875rem] text-gray-dark mt-7">Contenido del objeto</p>
                    <Button
                        disabled={!element.type}
                        onClick={createObject}
                        text="Crear"
                        buttonClassName="absolute bottom-7 right-7"
                    />
                    {ElementToToShow && <ElementToToShow element={element} />}
                </div>
                {!isSelectTable && <SidebarTools />}
            </div>
            {isDataSend && requiredFieldsEmpty && <Toast type={NotificationType.Error} onClose={() => {}} open />}
            {
                <Toast
                    type={NotificationType.Error}
                    message={errorMessage}
                    onClose={() => {}}
                    open={!!errorMessage && !element?.content}
                />
            }
            {showReturnModal && (
                <DialogModal
                    onConfirm={handleReturnModal}
                    onClose={() => {
                        setShowReturnModal(false);
                    }}
                    type={DialogModalType.ReturnObjectPage}
                />
            )}
        </>
    );
};
