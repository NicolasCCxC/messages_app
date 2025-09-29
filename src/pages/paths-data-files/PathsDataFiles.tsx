import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { createPathDataFile, getPathsDataFile, modifyPathDataFile } from '@redux/paths-data-files/actions';
import { getAllProducts } from '@redux/product-management/actions';
import { RootState } from '@redux/rootReducer';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { TextInput } from '@components/text-input';
import { Button } from '@components/button';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { Table } from '@components/table';
import { IOption } from '@components/select-search';
import { NotificationType, Toast } from '@components/toast';
import { ITEMS_PER_PAGE } from '@constants/Paginator';
import { hasEmptyFields } from '@utils/Array';
import { CreateDataModal } from './CreateDataModal';
import { BREADCRUMB_ITEMS, DEFAULT_FORM_VALUES, TABLE_FIELDS } from '.';
import { getDiff } from '@utils/Diff';
import { IGenericRecord } from '@models/GenericRecord';

const PathsDataFiles: React.FC = () => {
    const dispatch = useAppDispatch();
    const {
        data: { totalPages },
        paths,
    } = useAppSelector((state: RootState) => state.pathsDataFiles);
    const { allProducts } = useAppSelector((state: RootState) => state.productManagement);

    const [openModal, setOpenModal] = useState(false);
    const [notificationMessage, setNotificationMessage] = useState('');
    const [showToast, setShowToast] = useState(false);

    const { data, onFieldChange, updateData } = useTableData(paths);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    useEffect(() => {
        dispatch(getPathsDataFile({ size: ITEMS_PER_PAGE }));
        dispatch(getAllProducts());
    }, [dispatch]);

    const toggleModal = (): void => setOpenModal(!openModal);

    const onPageChange = useCallback(
        (page: number, search: string) => {
            dispatch(getPathsDataFile({ size: ITEMS_PER_PAGE, page, search }));
        },
        [dispatch]
    );

    const createData = async (
        formValues: IGenericRecord,
        selectedOptionProduct: IOption,
        selectOptionState: IOption
    ): Promise<IGenericRecord> => {
        const response = await dispatch(
            createPathDataFile({
                ...formValues,
                productId: selectedOptionProduct.value,
                active: JSON.parse(selectOptionState.value as string),
            })
        );
        updateData((response.payload as IGenericRecord).content);
        return response;
    };

    const onUpdateRow = useCallback(
        async (id: string) => {
            const itemEdit = data.find(item => item.id === id) ?? DEFAULT_FORM_VALUES;
            const originalItem = paths.find(item => item.id === id);

            if (hasEmptyFields(itemEdit)) {
                setNotificationMessage(REQUIRED_FIELDS);
                setShowToast(true);
                return;
            }

            const diff = getDiff<IGenericRecord>(originalItem, itemEdit, {
                customComparators: {
                    active: (orig, mod) => orig === JSON.parse(mod),
                },
                ignoreKeys: ['activeItem', 'edit', 'id', 'updateAt'],
            });

            const payload = {
                id: itemEdit.id,
                ...diff,
                ...(diff.product && { productId: diff?.product }),
                ...(diff.active && { active: JSON.parse(diff.active) }),
            };

            delete payload.product;

            const {
                // @ts-expect-error Property 'message' does not exist on type 'unknown'.
                payload: { content, message },
            } = await dispatch(modifyPathDataFile(payload));

            setNotificationMessage(message);
            setShowToast(true);
            updateData(
                data.map(row =>
                    row.id === itemEdit.id
                        ? { ...itemEdit, edit: false }
                        : { ...row, active: content?.find((item: IGenericRecord) => item.id === row.id).active ?? false }
                )
            );
        },
        [data, paths, dispatch, updateData]
    );

    const handleMessageToast = (message: string): void => {
        setNotificationMessage(message);
    };

    const toggleToast = (): void => setShowToast(!showToast);

    const dataProps = useMemo(
        () => ({ all: [...paths, ...allProducts], current: data, pages: totalPages, update: updateData }),
        [allProducts, data, paths, totalPages, updateData]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const handleSearch = (): void => {
        dispatch(getPathsDataFile({ search: searchValue }));
        displaySearchMessage();
    };

    return (
        <div>
            <Title title="Rutas para archivos de datos" className="mb-4.5" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5" />
            <div className="flex items-center mb-7">
                <TextInput
                    placeholder="Producto/ Ruta archivo entrada/ Ruta archivos procesados/ Estado"
                    inputClassName="h-[1.75rem]"
                    wrapperClassName="mr-2 w-72.5"
                    value={searchValue}
                    isSearch
                    onChange={handleSearchChange}
                />
                <Button text="Consultar" onClick={handleSearch} />
                <Button text="Crear" onClick={toggleModal} isIcon buttonClassName="ml-auto md:mr-[3.375rem]" />
            </div>
            <Table data={dataProps} fields={TABLE_FIELDS(allProducts as IOption[])} editing={editingProps} search={searchProps} />
            <Toast
                message={notificationMessage}
                open={showToast}
                onClose={toggleToast}
                {...(notificationMessage === REQUIRED_FIELDS && { type: NotificationType.Error })}
            />

            {openModal && (
                <CreateDataModal
                    toggleModal={toggleModal}
                    createData={createData}
                    products={allProducts as IOption[]}
                    toggleToast={toggleToast}
                    handleMessageToast={handleMessageToast}
                />
            )}
        </div>
    );
};

export default PathsDataFiles;
