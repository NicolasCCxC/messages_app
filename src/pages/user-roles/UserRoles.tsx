import { useCallback, useEffect, useMemo, useState } from 'react';
import { Breadcrumb } from '@components/breadcrumb';
import { Button } from '@components/button';
import { Form } from '@components/form';
import { Table } from '@components/table';
import { TextInput } from '@components/text-input';
import { Title } from '@components/title';
import { NotificationType, Toast } from '@components/toast';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getUserRoles, updateRole } from '@redux/user-roles/actions';
import { IGenericRecord } from '@models/GenericRecord';
import { DEFAULT_ROLE, ROUTES, TABLE_FIELDS } from '.';

const UserRoles: React.FC = () => {
    const dispatch = useAppDispatch();
    const { allData, pages } = useAppSelector(state => state.roles);

    const { data, onFieldChange, updateData } = useTableData(allData);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();
    const [notificationMessage, setNotificationMessage] = useState('');

    useEffect(() => {
        dispatch(getUserRoles({ page: 0 }));
    }, [dispatch]);

    const getActiveItem = useCallback((id: string) => data.find(item => item.id === id) ?? DEFAULT_ROLE, [data]);

    const getEditedProperties = useCallback(
        ({ active, description, id }: IGenericRecord) => {
            const oldRol = allData.find((item: IGenericRecord) => item.id === id);
            const properties: IGenericRecord = { id };

            if (oldRol?.description !== description) properties.description = description;
            if (oldRol?.active !== active) properties.active = active;

            return properties;
        },
        [allData]
    );

    const onPageChange = useCallback((page: number, search: string) => dispatch(getUserRoles({ page, search })), [dispatch]);

    const onUpdateRow = useCallback(
        async (id: string) => {
            const activeItem = getActiveItem(id);
            const editedProperties = getEditedProperties(activeItem);
            if (!activeItem?.description) return setNotificationMessage(REQUIRED_FIELDS);
            const { payload }: IGenericRecord = await dispatch(updateRole(editedProperties));
            setNotificationMessage(payload.message);
        },
        [dispatch, getActiveItem, getEditedProperties]
    );

    const dataProps = useMemo(
        () => ({ all: allData, current: data, pages: pages ?? 0, update: updateData }),
        [allData, data, pages, updateData]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const filterData = (): void => {
        dispatch(getUserRoles({ search: searchValue }));
        displaySearchMessage();
    };

    return (
        <div>
            <Title title="Gestión de roles de usuario" />
            <Breadcrumb items={ROUTES} />
            <Form className="flex items-center mt-[1.125rem] mb-7 w-max gap-2">
                <TextInput
                    onChange={handleSearchChange}
                    placeholder="Código de rol/ Descripción de rol/ Estado de rol"
                    value={searchValue}
                    wrapperClassName="w-[18.125rem]"
                    isSearch
                />
                <Button text="Consultar" onClick={filterData} />
            </Form>
            <Table data={dataProps} fields={TABLE_FIELDS} editing={editingProps} search={searchProps} />
            {notificationMessage && (
                <Toast
                    open
                    onClose={() => setNotificationMessage('')}
                    message={notificationMessage}
                    {...(notificationMessage === REQUIRED_FIELDS && { type: NotificationType.Error })}
                />
            )}
        </div>
    );
};

export default UserRoles;
