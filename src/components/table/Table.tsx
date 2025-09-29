import { memo, useMemo } from 'react';
import { PaginationBoundaries } from '@constants/Paginator';
import { ITableProps } from '@models/Table';
import { Body, Header, Paginator } from './components';
import { TableProvider } from './context';

export const Table: React.FC<ITableProps> = memo(({ customIcons, data, editing, fields, search, wrapperClassName = '' }) => {
    const { body, header, required } = fields;
    const { onFieldChange = (): void => {}, ...editingProps } = editing;

    const value = useMemo(() => ({ data, editing: editingProps }), [data, editingProps]);

    return (
        <TableProvider value={value}>
            <div className={`w-max ${wrapperClassName}`}>
                <table>
                    <Header fields={header} />
                    <Body customIcons={customIcons} fields={body} onFieldChange={onFieldChange} requiredFields={required} />
                </table>
                {search.showMessage && !data.current.length && (
                    <p className="mt-2 text-lg text-gray-dark">*No se han encontrado resultados para esta búsqueda</p>
                )}
                {data.pages > PaginationBoundaries.MinPage && <Paginator searchValue={search.value} />}
            </div>
        </TableProvider>
    );
});
