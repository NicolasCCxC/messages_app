import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { Breadcrumb } from '@components/breadcrumb';
import { Button } from '@components/button';
import { Form } from '@components/form';
import { Table } from '@components/table';
import { TextInput } from '@components/text-input';
import { Title } from '@components/title';
import { NotificationType, Toast } from '@components/toast';
import { REQUIRED_FIELDS as REQUIRED_FIELDS_MESSAGE } from '@constants/Validation';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { IGenericRecord } from '@models/GenericRecord';
import { getAllProducts } from '@redux/product-management/actions';
import { deleteInput, getInputs, updateInput } from '@redux/product-input/actions';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { hasEmptyFields, isEmptyObject } from '@utils/Object';
import { CreateRecordModal, REQUIRED_FIELDS, ROUTES, getTableFields } from '.';

const ProductInput: React.FC = () => {
    const dispatch = useAppDispatch();
    const { inputs, pages } = useAppSelector(state => state.productInput);
    const { allProducts } = useAppSelector(state => state.productManagement);

    const [saveError, setSaveError] = useState(false);
    const [notification, setNotification] = useState('');
    const [openModal, setOpenModal] = useState(false);

    const parseInputs = useMemo(
        () =>
            inputs?.map(item => ({
                id: item.id,
                productId: item.productId,
                fieldName: item.fieldName,
                registrationIdentifier: item.registrationIdentifier,
                typeRegister: item.typeRegister,
                initialPosition: item.initialPosition,
                endPosition: item.endPosition,
                indexFileIdentifier: item.indexFileIdentifier,
                fieldType: item.type,
            })),
        [inputs]
    );

    const { data, onFieldChange, updateData } = useTableData(parseInputs);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    useEffect(() => {
        ((): void => {
            Promise.all([dispatch(getAllProducts()), dispatch(getInputs({ page: 0 }))]);
        })();
    }, [dispatch]);

    const filterData = (e: FormEvent): void => {
        e.preventDefault();
        dispatch(getInputs({ search: searchValue }));
        displaySearchMessage();
    };

    const onPageChange = useCallback((page: number, search: string) => dispatch(getInputs({ page, search })), [dispatch]);

    const toggleModal = (): void => setOpenModal(prev => !prev);

    const onDeleteRow = useCallback(
        async (id: string) => {
            const { payload }: IGenericRecord = await dispatch(deleteInput(id));
            if (!payload?.data) return handleResponse(payload);
            setSaveError(false);
            setNotification(payload?.message);
        },
        [dispatch]
    );

    const hasValidationErrors = (item: IGenericRecord): boolean => {
        const isInvalid = isEmptyObject(item) || hasEmptyFields(item, REQUIRED_FIELDS);
        if (isInvalid) {
            setNotification(REQUIRED_FIELDS_MESSAGE);
            setSaveError(true);
        }
        return isInvalid;
    };

    const handleResponse = ({ data, message }: IGenericRecord): void => {
        !data ? setSaveError(true) : setSaveError(false);
        setNotification(message);
    };

    const onUpdateRow = useCallback(
        async (id: string) => {
            const itemToEdit = data.find(item => item.id === id) ?? {};
            if (hasValidationErrors(itemToEdit)) return;

            const { payload }: IGenericRecord = await dispatch(updateInput({ ...itemToEdit, type: itemToEdit.fieldType }));
            handleResponse(payload);
        },
        [data, dispatch]
    );

    const dataProps = useMemo(
        () => ({ all: parseInputs, current: data, pages, update: updateData }),
        [parseInputs, pages, updateData, data]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onDeleteRow, onPageChange, onUpdateRow }),
        [onFieldChange, onDeleteRow, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const tableFields = useMemo(() => getTableFields(allProducts), [allProducts]);

    const updateNotification = (notification: string): void => setNotification(notification);

    return (
        <div>
            <Title title="Definición de estructura de entrada por producto" />
            <Breadcrumb items={ROUTES} />
            <div className="flex items-center mt-[1.125rem] mb-7 justify-between">
                <Form className="flex items-center gap-2">
                    <TextInput
                        onChange={handleSearchChange}
                        placeholder="Producto/ Tipo de registro/ Nombre del campo/ Posición/ Identificador"
                        value={searchValue}
                        wrapperClassName="w-[18.125rem]"
                        isSearch
                    />
                    <Button text="Consultar" type="submit" onClick={filterData} />
                </Form>
                <Button buttonClassName="h-[1.875rem]" text="Crear" isIcon onClick={toggleModal} />
            </div>
            <Table data={dataProps} fields={tableFields} editing={editingProps} search={searchProps} />
            {openModal && (
                <CreateRecordModal products={allProducts} toggleModal={toggleModal} updateNotification={updateNotification} />
            )}
            {notification && (
                <Toast
                    open
                    onClose={() => setNotification('')}
                    message={notification}
                    {...(saveError && { type: NotificationType.Error })}
                />
            )}
        </div>
    );
};

export default ProductInput;
