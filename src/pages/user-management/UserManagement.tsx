import { useCallback, useEffect, useMemo, useState } from 'react';
import { RootState } from '@redux/rootReducer';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getUserManagement, modifyUserManagement } from '@redux/user-management/actions';
import { getUserRoles } from '@redux/user-roles/actions';
import { Breadcrumb } from '@components/breadcrumb';
import { Button } from '@components/button';
import { Table } from '@components/table';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { TextInput } from '@components/text-input';
import { Title } from '@components/title';
import { NotificationType, Toast } from '@components/toast';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { getTableFields, ROUTES, UserModal } from '.';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { IGenericRecord } from '@models/GenericRecord';
import { getDiff } from '@utils/Diff';
import { hasEmptyFields } from '@utils/Array';

const UserManagement: React.FC = () => {
    const dispatch = useAppDispatch();
    const {
        data: { totalPages },
        users,
    } = useAppSelector((state: RootState) => state.userManagement);
    const { allData: rolesData } = useAppSelector((state: RootState) => state.roles);
    const [openModal, setOpenModal] = useState(false);
    const [toastConfig, setToastConfig] = useState<{
        message: string;
        type?: NotificationType;
        show: boolean;
    }>({ message: '', type: undefined, show: false });

    const { data, onFieldChange, updateData } = useTableData(users);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    useEffect(() => {
        dispatch(getUserManagement({ size: ITEMS_PER_PAGE }));
        dispatch(getUserRoles({ page: 0 }));
    }, [dispatch]);

    const showNotification = (message: string, type: NotificationType | undefined): void => {
        setToastConfig({ message, type, show: true });
    };

    const onUpdateRow = useCallback(
        async (id: string) => {
            const activeItem = data.find(item => item?.edit && item.id === id);
            if (!activeItem || hasEmptyFields(activeItem)) {
                showNotification(REQUIRED_FIELDS, NotificationType.Error);
                return;
            }

            const originalItem = users.find(item => item.id === id);
            if (!originalItem) return;

            const modifiedItem = {
                ...originalItem,
                ...activeItem,
                name: activeItem.userName,
            };

            const diff = getDiff<IGenericRecord>(originalItem, modifiedItem, {
                ignoreKeys: ['id', 'createdAt', 'updateAt', 'activeItem', 'edit', 'userName'],
                customComparators: {
                    roles: (originalValue, modifiedValue) => JSON.stringify(originalValue) === JSON.stringify(modifiedValue),
                },
            });

            const payload: IGenericRecord = {};

            for (const key in diff) {
                if (Object.prototype.hasOwnProperty.call(diff, key)) {
                    if (key === 'roles') {
                        payload.roleCodes = diff.roles.map((role: IGenericRecord) => role.code);
                    } else {
                        payload[key] = diff[key];
                    }
                }
            }

            const response = await dispatch(modifyUserManagement({ id, ...payload }));

            if ('error' in response) {
                const jsonString = (response.payload as string).replace(/^Error:\s*/, '');
                const parsed = JSON.parse(jsonString);
                const message = parsed.message;
                showNotification(message, NotificationType.Error);
                return;
            }
            // @ts-expect-error Property 'message' does not exist on type 'unknown'.
            showNotification(response.payload?.message);
        },
        [data, users, dispatch]
    );

    const onPageChange = useCallback(
        (page: number, search: string) => {
            dispatch(getUserManagement({ size: ITEMS_PER_PAGE, page, search }));
        },
        [dispatch]
    );

    const handleSearch = useCallback(() => {
        dispatch(getUserManagement({ search: searchValue }));
        displaySearchMessage();
    }, [dispatch, searchValue, displaySearchMessage]);

    const dataProps = useMemo(
        () => ({ all: users, current: data, pages: totalPages, update: updateData }),
        [data, totalPages, updateData, users]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const toggleModal = (): void => setOpenModal(!openModal);
    const toggleToast = (): void => setToastConfig({ message: '', type: undefined, show: false });

    return (
        <div>
            <Title title="Gestión de usuarios" />
            <Breadcrumb items={ROUTES} />
            <div className="flex items-center mt-[1.125rem] mb-7 justify-between">
                <div className="flex items-center gap-2">
                    <TextInput
                        onChange={handleSearchChange}
                        placeholder="Usuario de red/ Nombres y apellidos/ Rol o roles/ Estado"
                        value={searchValue}
                        wrapperClassName="w-[18.125rem]"
                        isSearch
                    />
                    <Button text="Consultar" onClick={handleSearch} />
                </div>
                <Button buttonClassName="h-[1.875rem]" text="Crear" isIcon onClick={toggleModal} />
            </div>
            <Table data={dataProps} fields={getTableFields(rolesData)} editing={editingProps} search={searchProps} />
            <Toast open={toastConfig.show} onClose={toggleToast} message={toastConfig.message} type={toastConfig.type} />
            {openModal && <UserModal toggleModal={toggleModal} handleNotification={showNotification} />}
        </div>
    );
};

export default UserManagement;
