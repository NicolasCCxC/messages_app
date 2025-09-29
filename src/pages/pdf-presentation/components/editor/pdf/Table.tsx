import { memo } from 'react';
import { IGenericRecord } from '@models/GenericRecord';
import { IPdfObject } from '@models/Pdf';

export const Table: React.FC<IPdfObject> = memo(({ element }) => {
    const { header, body } = element;
    return (
        <div className="w-full max-w-[37.5rem] cursor-pointer relative">
            <table className="w-full border-collapse">
                <Header columns={header?.columns || []} />
                <Body cells={body?.cells || []} />
            </table>
        </div>
    );
});

const Header: React.FC<{ columns: IGenericRecord[] }> = memo(({ columns }) => (
    <thead>
        <tr>
            {columns.map((col, index) => (
                <th
                    key={index}
                    colSpan={col.colSpan ?? 1}
                    rowSpan={col.rowSpan ?? 1}
                    className="box-border p-2 overflow-hidden bg-gray-100 border border-[#000] w-[8.3333rem] h-[2.3125rem]"
                    style={col.style}
                >
                    {col.label}
                </th>
            ))}
        </tr>
    </thead>
));

const Body: React.FC<{ cells: IGenericRecord[] }> = memo(({ cells }) => {
    if (!cells || cells.length === 0) {
        return <tbody />;
    }

    const maxRows = Math.max(...cells.map(cell => cell.rowIndex)) + 1;
    const maxCols = Math.max(...cells.map(cell => cell.columnIndex)) + 1;

    const grid: (IGenericRecord | null)[][] = Array.from({ length: maxRows }, () => Array(maxCols).fill(null));

    cells.forEach(cell => {
        grid[cell.rowIndex][cell.columnIndex] = cell;
    });

    return (
        <tbody>
            {grid.map((row, rowIndex) => (
                <tr key={rowIndex}>
                    {row.map((cell, colIndex) => (
                        <td
                            key={colIndex}
                            colSpan={cell?.colSpan ?? 1}
                            rowSpan={cell?.rowSpan ?? 1}
                            className="box-border p-2 overflow-hidden text-center border border-[#000] w-[8.3333rem] h-[2.3125rem]"
                            style={cell?.style}
                        >
                            {cell?.content ?? ''}
                        </td>
                    ))}
                </tr>
            ))}
        </tbody>
    );
});