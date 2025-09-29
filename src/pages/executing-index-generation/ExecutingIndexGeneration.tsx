import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getIndex } from '@redux/executing-index-generation/actions';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { TextInput } from '@components/text-input';
import { Button } from '@components/button';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { Table } from '@components/table';
import { NotificationType, Toast } from '@components/toast';
import { TIME_TO_GET_DATA } from '@constants/Validation';
import { ExecutingIndexGenerationModal } from './ExecutingIndexGenerationModal';
import { BREADCRUMB_ITEMS, TABLE_FIELDS } from '.';

const ExecutingIndexGeneration: React.FC = () => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const {
        elements,
        data: { totalPages },
    } = useAppSelector(state => state.executingIndexGeneration);
    const dispatch = useAppDispatch();
    
    const [openModal, setOpenModal] = useState(false);
    const [notificationMessage, setNotificationMessage] = useState('');
    const [currentPage, setCurrentPage] = useState(0);
    const [currentSearchValue, setCurrentSearchValue] = useState('');
    const [showToast, setShowToast] = useState(false);
    const [notificationType] = useState<NotificationType>();

    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();
    const { onFieldChange, updateData } = useTableData(elements);

    useEffect(() => {
        dispatch(getIndex({}));
    }, [dispatch]);

    useEffect(() => {
        const interval = setInterval(() => {
            dispatch(getIndex({ page: currentPage, size: ITEMS_PER_PAGE, search: currentSearchValue }));
        }, TIME_TO_GET_DATA);

        return (): void => clearInterval(interval);
    }, [dispatch, currentPage, currentSearchValue]);

    const toggleModal = (): void => setOpenModal(!openModal);

    const toggleToast = (): void => setShowToast(!showToast);

    const handleMessageToast = (message: string): void => {
        setNotificationMessage(message);
    };

    const handleSearch = (): void => {
        setCurrentSearchValue(searchValue);
        setCurrentPage(0);
        dispatch(getIndex({ size: ITEMS_PER_PAGE, search: searchValue }));
        displaySearchMessage();
    };

    const onUpdateRow = useCallback(async (id: string) => {
        console.log(id);
    }, []);

    const onPageChange = useCallback(
        (page: number, search: string) => {
            setCurrentPage(page);
            dispatch(getIndex({ size: ITEMS_PER_PAGE, page, search }));
        },
        [dispatch]
    );

    const dataProps = useMemo(
        () => ({ all: elements, current: elements, pages: totalPages || 0, update: updateData }),
        [updateData, elements, totalPages]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    return (
        <div>
            <Title title="Ejecución del proceso de archivo de índices" className="mb-4.5" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5" />
            <div className="flex items-center mb-7">
                <TextInput
                    placeholder="Producto/ Fecha/ Usuario/ Estado/ % avance"
                    inputClassName="h-7.5"
                    wrapperClassName="mr-2 w-72.5"
                    value={searchValue}
                    isSearch
                    onChange={handleSearchChange}
                />
                <Button text="Consultar" onClick={handleSearch} />
                <Button
                    text="Cargar archivo fuente"
                    onClick={toggleModal}
                    isIcon
                    buttonClassName="ml-auto md:mr-[3.375rem] !w-[11.125rem]"
                    textClassName="!w-[7.875rem]"
                />
            </div>
            <Table data={dataProps} fields={TABLE_FIELDS(allProducts)} editing={editingProps} search={searchProps} />
            <Toast message={notificationMessage} type={notificationType} open={showToast} onClose={toggleToast} />
            {openModal && (
                <ExecutingIndexGenerationModal
                    handleMessageToast={handleMessageToast}
                    toggleModal={toggleModal}
                    toggleToast={toggleToast}
                />
            )}
        </div>
    );
};

export default ExecutingIndexGeneration;
