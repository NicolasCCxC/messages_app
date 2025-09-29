import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { apiGetAuditConsultation } from '@api/AuditConsultation';
import { IGenericRecord } from '@models/GenericRecord';
import { urls } from '@api/Urls';
import { FetchRequest } from '@models/Request';
import { useTableData } from '@hooks/useTableData';
import { useTableSearch } from '@hooks/useTableSearch';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { TextInput } from '@components/text-input';
import { Button } from '@components/button';
import { Table } from '@components/table';
import { Icon } from '@components/icon';
import { handleExportCSV, handleExportPDF } from '@utils/DownloadDataTale';
import { BREADCRUMB_ITEMS, parseHtmlEntitiesToJson, TABLE_FIELDS } from '.';

const AuditConsultation: React.FC = () => {
    const [allData, setAllData] = useState([]);
    const [pages, setPages] = useState(0);
    const { data, onFieldChange, updateData } = useTableData(allData);
    const { displaySearchMessage, handleSearchChange, searchValue, showSearchMessage } = useTableSearch();

    const [parseData, setParseData] = useState(
        data.map(item => ({
            ...item,
            newValue: parseHtmlEntitiesToJson(item.newValue),
            prevValue: parseHtmlEntitiesToJson(item.prevValue),
        }))
    );

    useEffect(() => {
        setParseData(
            data.map(item => ({
                ...item,
                newValue: parseHtmlEntitiesToJson(item.newValue),
                prevValue: parseHtmlEntitiesToJson(item.prevValue),
            }))
        );
    }, [data, allData]);

    const fetchAuditData = async (page?: number, search?: string): Promise<void> => {
        const request = new FetchRequest(urls.auditConsultation.get({ search, page }));
        /* eslint-disable @typescript-eslint/no-explicit-any */
        const { data }: any = await apiGetAuditConsultation(request);
        setPages(data.totalPages);
        setAllData(data.content.map((item: IGenericRecord) => ({ ...item, userName: item.user.name })));
    };

    useEffect(() => {
        fetchAuditData();
    }, []);

    const handleSearch = async (): Promise<void> => {
        displaySearchMessage();
        await fetchAuditData(0, searchValue);
    };

    const onUpdateRow = useCallback(async () => {}, []);

    const onPageChange = useCallback(async (page: number, search: string) => {
        await fetchAuditData(page, search);
    }, []);

    const dataProps = useMemo(
        () => ({ all: allData, current: parseData, pages, update: updateData }),
        [updateData, allData, pages, parseData]
    );

    const editingProps = useMemo(
        () => ({ onFieldChange, onPageChange, onUpdateRow }),
        [onFieldChange, onUpdateRow, onPageChange]
    );

    const searchProps = useMemo(() => ({ showMessage: showSearchMessage, value: searchValue }), [searchValue, showSearchMessage]);

    return (
        <div>
            <Title title="Consulta de auditoria" className="mb-4.5" />
            <Breadcrumb items={BREADCRUMB_ITEMS} className="mb-4.5" />
            <div className="flex items-center mb-7">
                <TextInput
                    placeholder="Fecha/ Usuario/ IP/ Acción realizada/ Valor anterior/ Valor nuevo"
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
                        onClick={() => handleExportCSV(TABLE_FIELDS, data)}
                        className="cursor-pointer"
                    />
                </div>
            </div>
            <Table data={dataProps} fields={TABLE_FIELDS} editing={editingProps} search={searchProps} />
        </div>
    );
};

export default AuditConsultation;
