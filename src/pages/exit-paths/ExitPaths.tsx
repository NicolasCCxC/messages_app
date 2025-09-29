import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { Breadcrumb } from '@components/breadcrumb';
import { Button } from '@components/button';
import { Form } from '@components/form';
import { TextInput } from '@components/text-input';
import { Table } from '@components/table';
import { Title } from '@components/title';
import { NotificationType, Toast } from '@components/toast';
import { REQUIRED_FIELDS as REQUIRED_FIELDS_MESSAGE } from '@constants/Validation';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { IGenericRecord } from '@models/GenericRecord';
import { deletePath, getExitPaths, updatePath } from '@redux/paths/actions';
import { getAllProducts } from '@redux/product-management/actions';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { hasEmptyFields } from '@utils/Object';
import { CreateRecordModal, getTableFields, REQUIRED_FIELDS, ROUTES } from '.';

const ExitPaths: React.FC = () => {
    const dispatch = useAppDispatch();
    const { allProducts } = useAppSelector(state => state.productManagement);
    const { paths, pages } = useAppSelector(state => state.paths);

    const [openModal, setOpenModal] = useState(false);
    const [notification, setNotification] = useState('');

    const { data, onFieldChange, updateData } = useTableData(paths);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    useEffect(() => {
        ((): void => {
            Promise.all([dispatch(getAllProducts()), dispatch(getExitPaths({ page: 0 }))]);
        })();
    }, [dispatch]);

    const filterData = (e: FormEvent): void => {
        e.preventDefault();
        dispatch(getExitPaths({ search: searchValue }));
        displaySearchMessage();
    };

    const onDeleteRow = useCallback(
        async (id: string) => {
            const { payload }: IGenericRecord = await dispatch(deletePath(id));
            setNotification(payload?.message);
        },
        [dispatch]
    );

    const onUpdateRow = useCallback(
        async (id: string) => {
            const itemToEdit = data.find(item => item.id === id);
            if (!itemToEdit || hasEmptyFields(itemToEdit, REQUIRED_FIELDS)) return setNotification(REQUIRED_FIELDS_MESSAGE);
            const { payload }: IGenericRecord = await dispatch(updatePath(itemToEdit));
            setNotification(payload.message);
        },
        [data, dispatch]
    );

    const onPageChange = useCallback((page: number, search: string) => dispatch(getExitPaths({ page, search })), [dispatch]);

    const availableProducts = useMemo(
        () => allProducts.filter(({ id }: IGenericRecord) => paths.every(({ productId }: IGenericRecord) => productId !== id)),
        [allProducts, paths]
    );

    const dataProps = useMemo(() => ({ all: paths, current: data, pages, update: updateData }), [data, paths, pages, updateData]);

    const editingProps = useMemo(
        () => ({ onFieldChange, onDeleteRow, onPageChange, onUpdateRow }),
        [onFieldChange, onDeleteRow, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const toggleModal = (): void => setOpenModal(prev => !prev);

    const tableFields = useMemo(() => getTableFields(allProducts, availableProducts), [allProducts, availableProducts]);

    const updateNotification = (notification: string): void => setNotification(notification);

    return (
        <>
            <Title title="Rutas para extractos y archivos de índices" />
            <Breadcrumb items={ROUTES} />
            <div className="flex items-center mt-[1.125rem] mb-7 justify-between">
                <Form className="flex items-center gap-2">
                    <TextInput
                        onChange={handleSearchChange}
                        placeholder="Producto/ Ruta salida extracto/ Ruta salida archivos de índices"
                        value={searchValue}
                        wrapperClassName="w-[18.125rem]"
                        isSearch
                    />
                    <Button text="Consultar" onClick={filterData} />
                </Form>
                <Button
                    buttonClassName="h-[1.875rem]"
                    text="Crear"
                    isIcon
                    onClick={() => {
                        updateData(paths);
                        toggleModal();
                    }}
                />
            </div>
            <Table data={dataProps} fields={tableFields} editing={editingProps} search={searchProps} />
            {openModal && (
                <CreateRecordModal
                    products={availableProducts}
                    toggleModal={toggleModal}
                    updateNotification={updateNotification}
                />
            )}
            {notification && (
                <Toast
                    open
                    onClose={() => setNotification('')}
                    message={notification}
                    {...(notification === REQUIRED_FIELDS_MESSAGE && { type: NotificationType.Error })}
                />
            )}
        </>
    );
};
export default ExitPaths;
