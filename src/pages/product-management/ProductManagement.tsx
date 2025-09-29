import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getProductManagement, modifyProductManagement } from '@redux/product-management/actions';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { Button } from '@components/button';
import { Table } from '@components/table';
import { TextInput } from '@components/text-input';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { NotificationType, Toast } from '@components/toast';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { hasEmptyFields } from '@utils/Array';
import { IGenericRecord } from '@models/GenericRecord';
import { getDiff } from '@utils/Diff';
import { CreateProductModal } from './CreateProductModal';
import { BREADCRUMB_ITEMS, DEFAULT_FORM_VALUES, TABLE_FIELDS } from '.';

const ProductManagement: React.FC = () => {
    const {
        data: { totalPages },
        products,
    } = useAppSelector(state => state.productManagement);

    const [openModal, setOpenModal] = useState(false);
    const [notificationMessage, setNotificationMessage] = useState('');
    const [showToast, setShowToast] = useState(false);
    const [notificationType, setNotificationType] = useState<NotificationType>();
    const dispatch = useAppDispatch();

    const { data, onFieldChange, updateData } = useTableData(products);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    useEffect(() => {
        dispatch(getProductManagement({ size: ITEMS_PER_PAGE }));
    }, [dispatch]);

    const toggleModal = (): void => setOpenModal(!openModal);

    const onUpdateRow = useCallback(
        async (id: string) => {
            const activeItem = data.find(item => item.id === id) ?? DEFAULT_FORM_VALUES;
            if (hasEmptyFields(activeItem)) {
                setNotificationType(NotificationType.Error);
                setNotificationMessage(REQUIRED_FIELDS);
                setShowToast(true);
                return;
            }
            const originalItem = products.find(item => item.id === id);

            const diff = getDiff<IGenericRecord>(originalItem, activeItem, {
                customComparators: {
                    active: (orig, mod) => orig === JSON.parse(mod),
                },
                ignoreKeys: ['id', 'updateAt', 'activeItem', 'edit'],
            });
            const response = await dispatch(
                modifyProductManagement({ id, ...diff, ...(diff.active && { active: JSON.parse(diff.active) }) })
            );

            if ('error' in response) {
                const jsonString = (response.payload as string).replace(/^Error:\s*/, '');
                const parsed = JSON.parse(jsonString);
                const message = parsed.message;
                setNotificationType(NotificationType.Error);
                setNotificationMessage(message);
                setShowToast(true);
                return;
            }

            setNotificationType(undefined);
            setNotificationMessage(response.payload.message);
            setShowToast(true);
        },
        [dispatch, data, products]
    );

    const onPageChange = useCallback(
        (page: number, search: string) => {
            dispatch(getProductManagement({ size: ITEMS_PER_PAGE, page, search }));
        },
        [dispatch]
    );

    const dataProps = useMemo(
        () => ({ all: products, current: data, pages: totalPages, update: updateData }),
        [data, products, totalPages, updateData]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const handleSearch = (): void => {
        dispatch(getProductManagement({ search: searchValue }));
        displaySearchMessage();
    };

    const handleMessageToast = (message: string): void => {
        setNotificationMessage(message);
    };

    const toggleToast = (): void => setShowToast(!showToast);

    return (
        <div>
            <Title title="Gestión de productos" className="mb-4.5" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5" />
            <div className="flex items-center mb-7">
                <TextInput
                    placeholder="Código del producto/Descripción del producto/ Document type/ Estado del producto"
                    inputClassName="h-7.5"
                    wrapperClassName="mr-2 w-72.5"
                    value={searchValue}
                    isSearch
                    onChange={handleSearchChange}
                />
                <Button text="Consultar" onClick={handleSearch} />
                <Button text="Crear" onClick={toggleModal} isIcon buttonClassName="ml-auto md:mr-[3.375rem]" />
            </div>
            <Table data={dataProps} fields={TABLE_FIELDS} editing={editingProps} search={searchProps} />
            <Toast message={notificationMessage} type={notificationType} open={showToast} onClose={toggleToast} />

            {openModal && (
                <CreateProductModal handleMessageToast={handleMessageToast} toggleModal={toggleModal} toggleToast={toggleToast} />
            )}
        </div>
    );
};

export default ProductManagement;
