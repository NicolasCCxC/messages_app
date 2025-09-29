import { useCallback, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { Button } from '@components/button';
import { TextInput } from '@components/text-input';
import { Table } from '@components/table';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { NotificationType, Toast } from '@components/toast';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { IOption } from '@components/select-search';
import { getAllProducts } from '@redux/product-management/actions';
import { deleteContentProduct, getManageContentProduct } from '@redux/manage-content-product/actions';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { IGenericRecord } from '@models/GenericRecord';
import { DialogModal } from '@components/modal';
import { IconName } from '@components/icon';
import { CreateDataModal } from './CreateDataModal';
import { BREADCRUMB_ITEMS, getTableFields } from '.';

const ManageContentProduct: React.FC = () => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const {
        content,
        manageData: { totalPages },
    } = useAppSelector(state => state.manageContentProduct);
    const dispatch = useAppDispatch();
    const { data, onFieldChange, updateData } = useTableData(content);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();
    const [openModal, setOpenModal] = useState(false);
    const [isModify, setIsModify] = useState(false);
    const [modifyData, setModifyData] = useState<IGenericRecord>({});
    const [notificationMessage, setNotificationMessage] = useState('');
    const [showToast, setShowToast] = useState(false);
    const [openDeleteModal, setOpenDeleteModal] = useState(false);
    const [itemToDelete, setItemToDelete] = useState<IGenericRecord>({});

    useEffect(() => {
        dispatch(getManageContentProduct({ size: ITEMS_PER_PAGE }));
        dispatch(getAllProducts());
    }, [dispatch]);

    const deleteRow = async (): Promise<void> => {
        if (itemToDelete.id) {
            const response = await dispatch(deleteContentProduct(itemToDelete.id));
            toggleDeleteModal();
            // @ts-expect-error Property 'message' does not exist on type 'unknown'.
            handleMessageToast(response.payload.message);
            setShowToast(true);
        }
    };

    const handleModify = useCallback((item: IGenericRecord) => {
        setOpenModal(true);
        setIsModify(true);
        setModifyData(item);
    }, []);

    const toggleDeleteModal = useCallback(() => setOpenDeleteModal(deleteModal => !deleteModal), []);

    const onDeleteRow = useCallback(
        (item: IGenericRecord) => {
            toggleDeleteModal();
            setItemToDelete(item);
        },
        [toggleDeleteModal]
    );

    const iconAction: { [key: string]: (item: IGenericRecord) => void } = useMemo(
        () => ({
            pencilBlue: handleModify,
            trashBlue: onDeleteRow,
        }),
        [handleModify, onDeleteRow]
    );

    const handleFormatAction = useCallback(
        (icon: IconName, item: IGenericRecord): void => {
            iconAction[icon](item);
        },
        [iconAction]
    );

    const onPageChange = useCallback(
        (page: number, search: string) => {
            dispatch(getManageContentProduct({ size: ITEMS_PER_PAGE, page, search }));
        },
        [dispatch]
    );

    const dataProps = useMemo(
        () => ({ all: content, current: data, pages: totalPages, update: updateData }),
        [data, updateData, content, totalPages]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onIconClick: handleFormatAction }),
        [onFieldChange, onPageChange, handleFormatAction]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const toggleModal = (): void => {
        setOpenModal(!openModal);
        setModifyData({});
        setIsModify(false);
    };

    const handleMessageToast = (message: string): void => {
        setNotificationMessage(message);
    };

    const toggleToast = (): void => setShowToast(!showToast);

    const handleSearch = (): void => {
        dispatch(getManageContentProduct({ search: searchValue }));
        displaySearchMessage();
    };

    return (
        <div>
            <Title title="Gestión del contenido del archivo de índice por producto" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5" />

            <div className="flex items-center mb-7">
                <TextInput
                    placeholder="Producto/ Campos requeridos/ Tipo de archivo/ Nombre archivo de índice"
                    inputClassName="h-7.5"
                    wrapperClassName="mr-2 w-72.5"
                    value={searchProps.value}
                    isSearch
                    onChange={handleSearchChange}
                />
                <Button text="Consultar" onClick={handleSearch} />
                <Button text="Crear" onClick={toggleModal} isIcon buttonClassName="ml-auto md:mr-[3.375rem]" />
            </div>
            <Table
                wrapperClassName="overflow-y-auto max-h-[25.4375rem]"
                data={dataProps}
                fields={getTableFields(allProducts)}
                editing={editingProps}
                search={searchProps}
            />
            <Toast
                message={notificationMessage}
                {...(notificationMessage === REQUIRED_FIELDS && { type: NotificationType.Error })}
                open={showToast}
                onClose={toggleToast}
            />
            {openModal && (
                <CreateDataModal
                    modifyData={modifyData}
                    isModify={isModify}
                    toggleToast={toggleToast}
                    products={allProducts as IOption[]}
                    handleMessageToast={handleMessageToast}
                    toggleModal={toggleModal}
                    handleUpdateData={updateData}
                />
            )}
            {openDeleteModal && <DialogModal onClose={toggleDeleteModal} onConfirm={deleteRow} />}
        </div>
    );
};

export default ManageContentProduct;
