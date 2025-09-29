import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getAssistedProcess } from '@redux/execution-assisted-process/actions';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { TextInput } from '@components/text-input';
import { Button } from '@components/button';
import { Table } from '@components/table';
import { NotificationType, Toast } from '@components/toast';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { TIME_TO_GET_DATA } from '@constants/Validation';
import { ExecutionAssistedProcessModal } from './ExecutionAssistedProcessModal';
import { BREADCRUMB_ITEMS, TABLE_FIELDS } from '.';

const ExecutionAssistedProcess: React.FC = () => {
    const {
        elements,
        data: { totalPages },
    } = useAppSelector(state => state.executionAssistedProcess);
    const dispatch = useAppDispatch();

    const [openModal, setOpenModal] = useState(false);
    const [notificationMessage, setNotificationMessage] = useState('');
    const [showToast, setShowToast] = useState(false);
    const [currentPage, setCurrentPage] = useState(0);
    const [currentSearchValue, setCurrentSearchValue] = useState('');
    const [notificationType, setNotificationType] = useState<NotificationType>();
    const [parseElements, setParseElements] = useState(elements);

    const { onFieldChange, updateData } = useTableData(elements);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    useEffect(() => {
        setParseElements(
            elements.map(item => ({
                ...item,
                user: item?.user?.email,
                product: item?.product?.code + ' - ' + item?.product?.description,
            }))
        );
    }, [elements]);

    useEffect(() => {
        dispatch(getAssistedProcess({}));
    }, [dispatch]);

    useEffect(() => {
        const interval = setInterval(() => {
            dispatch(getAssistedProcess({ page: currentPage, size: ITEMS_PER_PAGE, search: currentSearchValue }));
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
        displaySearchMessage();
        dispatch(getAssistedProcess({ search: searchValue }));
    };

    const onUpdateRow = useCallback(async (id: string) => {
        setNotificationType(NotificationType.Error);
        console.log(id);
    }, []);

    const onPageChange = useCallback(
        (page: number, search: string) => {
            setCurrentPage(page);
            dispatch(getAssistedProcess({ page, search }));
        },
        [dispatch]
    );

    const dataProps = useMemo(
        () => ({ all: parseElements, current: parseElements, pages: totalPages, update: updateData }),
        [parseElements, updateData, totalPages]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    return (
        <div>
            <Title title="Ejecución del Proceso de Generación de Extractos Asistido" className="mb-4.5" />
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
                    text="Generar extractos"
                    onClick={toggleModal}
                    isIcon
                    buttonClassName="ml-auto md:mr-[3.375rem] !w-[9.8125rem]"
                    textClassName="!w-[6.5625rem]"
                />
            </div>
            <Table data={dataProps} fields={TABLE_FIELDS()} editing={editingProps} search={searchProps} />
            <Toast message={notificationMessage} type={notificationType} open={showToast} onClose={toggleToast} />
            {openModal && (
                <ExecutionAssistedProcessModal
                    handleMessageToast={handleMessageToast}
                    toggleModal={toggleModal}
                    toggleToast={toggleToast}
                />
            )}
        </div>
    );
};

export default ExecutionAssistedProcess;
