import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { Button } from '@components/button';
import { Form } from '@components/form';
import { Table } from '@components/table';
import { TextInput } from '@components/text-input';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { IGenericRecord } from '@models/GenericRecord';
import { getFormats } from '@redux/pdf/actions';
import { openSidebar } from '@redux/sidebar/sidebarSlice';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { getTableFields, ModalView, IFormatListProps, TableIcons } from '.';

export const FormatList: React.FC<IFormatListProps> = ({ toggleEditor }) => {
    const dispatch = useAppDispatch();

    const { allProducts } = useAppSelector(state => state.productManagement);
    const { formats, pages } = useAppSelector(state => state.pdf);

    const [isOpenVisualization, setIsOpenVisualization] = useState(false);

    const { data, updateData } = useTableData(formats);
    const { handleSearchChange, searchValue, showSearchMessage, displaySearchMessage } = useTableSearch();

    useEffect(() => {
        dispatch(getFormats({ page: 0 }));
        dispatch(openSidebar());
    }, [dispatch]);

    const onPageChange = useCallback((page: number, search: string) => dispatch(getFormats({ page, search })), [dispatch]);

    const dataProps = useMemo(
        () => ({ all: data, current: formats, pages, update: updateData }),
        [data, formats, pages, updateData]
    );

    const editingProps = useMemo(() => ({ onPageChange }), [onPageChange]);

    const handleSearchSubmit = (e: FormEvent): void => {
        e.preventDefault();
        dispatch(getFormats({ search: searchValue }));
        displaySearchMessage();
    };

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    const tableFields = useMemo(() => getTableFields(allProducts), [allProducts]);

    const toggleVisualization = useCallback((): void => setIsOpenVisualization(prev => !prev), []);

    const renderCustomIcons = useCallback(
        (item: IGenericRecord): JSX.Element => (
            <TableIcons item={item} toggleEditor={toggleEditor} toggleVisualization={toggleVisualization} />
        ),
        [toggleEditor, toggleVisualization]
    );

    return (
        <div className="pl-[2.375rem]">
            <div className="flex items-center mt-[1.125rem] mb-7 justify-between max-w-[48rem]">
                <Form className="flex items-center gap-2">
                    <TextInput
                        onChange={handleSearchChange}
                        placeholder="Producto/ Versión del formato/ Estado formato"
                        value={searchValue}
                        wrapperClassName="w-[18.125rem]"
                        isSearch
                    />
                    <Button onClick={handleSearchSubmit} text="Consultar" type="submit" />
                </Form>
                <Button buttonClassName="h-[1.875rem]" isIcon onClick={toggleEditor} text="Crear" />
            </div>
            <Table
                customIcons={renderCustomIcons}
                data={dataProps}
                fields={tableFields}
                editing={editingProps}
                search={searchProps}
            />
            {isOpenVisualization && <ModalView toggleModal={toggleVisualization} />}
        </div>
    );
};
