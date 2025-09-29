import { useCallback, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { deleteObject, getObjectManageFormat, getOneObject } from '@redux/object-manage-format/actions';
import { resetElement } from '@redux/object-manage-format/objectManageFormatSlice.ts';
import { IGenericRecord } from '@models/GenericRecord';
import { Breadcrumb } from '@components/breadcrumb';
import { TextInput } from '@components/text-input';
import { Title } from '@components/title';
import { Table } from '@components/table';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { IS_EDITOR_OPEN } from '@constants/Text';
import { Button } from '@components/button';
import { IconName } from '@components/icon';
import { NotificationType, Toast } from '@components/toast';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { DialogModal, DialogModalType } from '@components/modal';
import localStorage from '@utils/LocalStorage.ts';
import { ObjectManageProvider } from './context/ObjectManageProvider';
import { Editor } from './components';
import { PreviewModal } from './PreviewModal';
import { BREADCRUMB_ITEMS, getTableFields } from '.';
import { ChangeEvent } from '@models/Input';

const ObjectManageFormat: React.FC = () => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const {
        data: { totalPages },
        elements,
        element,
    } = useAppSelector(state => state.objectManageFormat);
    const { data, onFieldChange, updateData } = useTableData(elements);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();
    const [showEditor, setShowEditor] = useState(false);
    const [isModify, setIsModify] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [itemId, setItemId] = useState('');
    const [notificationMessage, setNotificationMessage] = useState('');
    const [showToast, setShowToast] = useState(false);
    const [saveError, setSaveError] = useState(false);
    const [openPreviewModal, setOpenPreviewModal] = useState(false);

    const dispatch = useAppDispatch();

    useEffect(() => {
        localStorage.set(IS_EDITOR_OPEN, 'false');
    }, []);

    useEffect(() => {
        dispatch(getObjectManageFormat({}));
    }, [showEditor, dispatch]);

    const toggleEditor = useCallback((): void => {
        handleSearchChange({ target: { value: '' } } as ChangeEvent);
        if (showEditor) dispatch(resetElement());
        setShowEditor(!showEditor);
        localStorage.set(IS_EDITOR_OPEN, String(!showEditor));
    }, [dispatch, showEditor]);

    const toggleModify = useCallback((): void => setIsModify(!isModify), [isModify]);

    const handleModify = useCallback(
        (item: IGenericRecord) => {
            dispatch(getOneObject(item.id));
            toggleEditor();
            toggleModify();
        },
        [toggleEditor, dispatch, toggleModify]
    );

    const togglePreviewModal = useCallback((): void => {
        setOpenPreviewModal(!openPreviewModal);
    }, [openPreviewModal]);

    const handlePreview = useCallback(
        (item: IGenericRecord) => {
            togglePreviewModal();
            dispatch(getOneObject(item.id));
        },
        [togglePreviewModal, dispatch]
    );

    const onDeleteRow = useCallback((item: IGenericRecord) => {
        setShowDeleteModal(true);
        setItemId(item.id);
    }, []);

    const handleDeleteItem = async (): Promise<void> => {
        const {
            payload: { data, message },
        }: IGenericRecord = await dispatch(deleteObject(itemId));
        if (!data) {
            setSaveError(true);
            setShowToast(true);
            setNotificationMessage(message);
        } else {
            setShowToast(true);
            setNotificationMessage(message);
            setSaveError(false);
        }

        setShowDeleteModal(false);
    };

    const iconAction: { [key: string]: (item: IGenericRecord) => void } = useMemo(
        () => ({
            pencilBlue: handleModify,
            eyeBlue: handlePreview,
            trashBlue: onDeleteRow,
        }),
        [handleModify, onDeleteRow, handlePreview]
    );

    const handleFormatAction = useCallback(
        (icon: IconName, item: IGenericRecord): void => {
            iconAction[icon](item);
        },
        [iconAction]
    );

    const onPageChange = useCallback(
        (page: number, search: string) => {
            dispatch(getObjectManageFormat({ size: ITEMS_PER_PAGE, page, search }));
        },
        [dispatch]
    );

    const dataProps = useMemo(
        () => ({ all: data, current: elements, pages: totalPages, update: updateData }),
        [data, updateData, totalPages, elements]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onIconClick: handleFormatAction }),
        [onFieldChange, onPageChange, handleFormatAction]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const handleMessageToast = (message: string): void => {
        setNotificationMessage(message);
    };

    const handleSearch = (): void => {
        dispatch(getObjectManageFormat({ search: searchValue }));
        displaySearchMessage();
    };

    const toggleToast = (): void => setShowToast(!showToast);

    return (
        <ObjectManageProvider>
            <Toast
                message={notificationMessage}
                open={showToast}
                onClose={toggleToast}
                {...(saveError && { type: NotificationType.Error })}
            />
            <Title title="Gestión de objetos del formato por tipo de producto" className="mb-4.5" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5 pl-[2.375rem]" />
            {showEditor ? (
                <Editor
                    elementToModify={element}
                    toggleModify={toggleModify}
                    toggleEditor={toggleEditor}
                    toggleToast={toggleToast}
                    setSaveError={setSaveError}
                    handleMessageToast={handleMessageToast}
                    isModify={isModify}
                />
            ) : (
                <div className="pl-[2.375rem]">
                    <div className="flex items-center mb-7 ">
                        <TextInput
                            placeholder="Producto/ Código del objeto/ Nombre del objeto"
                            wrapperClassName="mr-2 w-72.5 max-h-[1.875rem]"
                            value={searchProps.value}
                            isSearch
                            onChange={handleSearchChange}
                        />
                        <Button text="Consultar" onClick={handleSearch} />
                        <Button text="Crear" onClick={toggleEditor} isIcon buttonClassName="ml-auto md:mr-[3.375rem]" />
                    </div>
                    <Table data={dataProps} fields={getTableFields(allProducts)} editing={editingProps} search={searchProps} />
                    {showDeleteModal && (
                        <DialogModal
                            onConfirm={handleDeleteItem}
                            onClose={() => {
                                setShowDeleteModal(false);
                            }}
                            type={DialogModalType.DeleteObject}
                        />
                    )}
                </div>
            )}
            {openPreviewModal && <PreviewModal togglePreviewModal={togglePreviewModal} />}
        </ObjectManageProvider>
    );
};

export default ObjectManageFormat;
