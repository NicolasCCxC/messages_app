import type { IGenericRecord } from '@models/GenericRecord';
import { IElement } from '@pages/object-manage-format/context';

export const useTableSelection = ({
    element,
}: {
    element: IElement;
}): {
    getHorizontalSelection: (
        start: {
            row: number;
            column: number;
        },
        end: {
            row: number;
            column: number;
        }
    ) => {
        row: number;
        column: number;
        style: IGenericRecord;
        colSpan: number;
    }[];
} => {
    const getHorizontalSelection = (
        start: { row: number; column: number },
        end: { row: number; column: number }
    ): { row: number; column: number; style: IGenericRecord; colSpan: number }[] => {
        if (start.row !== end.row) return [];

        const minCol = Math.min(start.column, end.column);
        const maxCol = Math.max(start.column, end.column);
        const row = start.row;

        if (row === -1) {
            return element?.header?.columns
                .map((col: IGenericRecord, index: number) => ({
                    row: -1,
                    column: index,
                    style: col.style,
                    colSpan: col.colSpan,
                }))
                .slice(minCol, maxCol + 1);
        } else {
            return element?.body?.cells
                .filter(
                    (cell: IGenericRecord) => cell.rowIndex === row && cell.columnIndex >= minCol && cell.columnIndex <= maxCol
                )
                .map((cell: IGenericRecord) => ({
                    row: cell.rowIndex,
                    column: cell.columnIndex,
                    style: cell.style,
                    colSpan: cell.colSpan,
                }));
        }
    };

    return {
        getHorizontalSelection,
    };
};
