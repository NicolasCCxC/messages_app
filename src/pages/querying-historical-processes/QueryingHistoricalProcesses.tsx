import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { apiGetHistoricalProcess } from '@api/QueryingHistoricalProcesses';
import { IGenericRecord } from '@models/GenericRecord';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { TextInput } from '@components/text-input';
import { Button } from '@components/button';
import { Table } from '@components/table';
import { useAppSelector } from '@redux/store';
import { handleExportCSV, handleExportPDF } from '@utils/DownloadDataTale';
import { FetchRequest } from '@models/Request';
import { urls } from '@api/Urls';
import { Icon } from '@components/icon';
import { BREADCRUMB_ITEMS, TABLE_FIELDS } from '.';

const QueryingHistoricalProcesses: React.FC = () => {
    const { allProducts } = useAppSelector(state => state.productManagement);
    const [allData, setAllData] = useState([]);
    const [pages, setPages] = useState(0);
    const { onFieldChange, updateData } = useTableData(allData);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    const fetchData = async (page?: number, search?: string): Promise<void> => {
        const request = new FetchRequest(urls.queryingHistoricalProcesses.get({ page, search }));
        /* eslint-disable @typescript-eslint/no-explicit-any */
        const { data }: any = await apiGetHistoricalProcess(request);
        setPages(data.totalPages);
        setAllData(
            data.content.map((item: IGenericRecord) => ({
                ...item,
                user: item.user.email,
                productName: allProducts.find(product => product.id === item.product.id)?.label || '',
            }))
        );
    };

    useEffect(() => {
        fetchData();
    }, []);

    const handleSearch = async (): Promise<void> => {
        await fetchData(0, searchValue);
        displaySearchMessage();
    };

    const onUpdateRow = useCallback(async () => {}, []);

    const onPageChange = useCallback(async (page: number, search: string) => {
        await fetchData(page, search);
    }, []);

    const dataProps = useMemo(
        () => ({ all: allData, current: allData, pages: pages, update: updateData }),
        [updateData, pages, allData]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    return (
        <div>
            <Title title="Consulta de históricos de proceso de extractos" className="mb-4.5" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5" />
            <div className="flex items-center mb-7">
                <TextInput
                    placeholder="Producto/ Fecha/ Usuario/ Cantidad de extractos"
                    inputClassName="h-7.5"
                    wrapperClassName="mr-2 w-72.5"
                    value={searchValue}
                    isSearch
                    onChange={handleSearchChange}
                />
                <Button text="Consultar" onClick={handleSearch} />
                <div className="flex ml-auto">
                    <Icon name="pdf" alt="Exportar a PDF" onClick={handleExportPDF} className="cursor-pointer mr-[1.1594rem]" />
                    <Icon
                        name="csv"
                        alt="Exportar a CSV"
                        onClick={() => handleExportCSV(TABLE_FIELDS, allData)}
                        className="cursor-pointer"
                    />
                </div>
            </div>
            <Table data={dataProps} fields={TABLE_FIELDS} editing={editingProps} search={searchProps} />
        </div>
    );
};

export default QueryingHistoricalProcesses;
