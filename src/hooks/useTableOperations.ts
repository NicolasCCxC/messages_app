import type { IGenericRecord } from '@models/GenericRecord';
import { IElement } from '@pages/object-manage-format/context';

/**
 * This defines the props used by the `useTableOperations` hook, which manages state and interactions
 * for a table element inside the editor.
 *
 * @typeParam element: IElement - The table element object being edited
 * @typeParam setElement: (element: IElement) => void - Function to update the element
 * @typeParam selectedCell: IBaseCell | null - The currently selected single cell
 * @typeParam setSelectedCell: (cell: IBaseCell | null) => void - Function to update the selected single cell
 * @typeParam selectedCells: ISelectableCell[] - A list of currently selected cells (for multi-selection)
 * @typeParam setSelectedCells: (cells: ISelectableCell[]) => void - Function to update the list of selected cells
 * @typeParam rows: IGenericRecord[][] - The current structure of the table rows
 */
interface IUseTableOperationsProps {
    element: IElement;
    setElement: (element: IElement) => void;
    selectedCell: { row: number; column: number; style: IGenericRecord } | null;
    setSelectedCell: (cell: { row: number; column: number; style: IGenericRecord } | null) => void;
    selectedCells: { row: number; column: number; style: IGenericRecord; colSpan: number }[];
    setSelectedCells: (cells: { row: number; column: number; style: IGenericRecord; colSpan: number }[]) => void;
    rows: IGenericRecord[][];
}

export const useTableOperations = ({
    element,
    setElement,
    selectedCell,
    setSelectedCell,
    selectedCells,
    setSelectedCells,
    rows,
}: IUseTableOperationsProps): {
    handleAddColumn: () => void;
    handleAddRow: () => void;
    handleUpdateHeaderCell: (colIndex: number, value: string) => void;
    handleUpdateBodyCell: (rowIndex: number, colIndex: number, value: string) => void;
    updateSelectedCellStyle: (styleUpdate: Partial<IGenericRecord['style']>) => void;
    handleMergeCells: () => void;
} => {
    const handleAddColumn = (): void => {
        const nextColumnIndex = element?.header?.columns.length;

        const updatedHeader = [
            ...(element?.header?.columns || []),
            {
                label: '',
                rowIndex: 0,
                columnIndex: nextColumnIndex,
                style: {},
            },
        ];

        const updatedCells = [
            ...(element?.body?.cells || []),
            ...Array.from({ length: rows.length }).map((_, rowIndex) => ({
                content: '',
                rowIndex,
                columnIndex: nextColumnIndex,
                style: {},
            })),
        ];

        setElement({
            ...element,
            header: { ...element.header, columns: updatedHeader },
            body: { ...element.body, cells: updatedCells },
        });
    };

    const handleAddRow = (): void => {
        const nextRowIndex = rows.length;
        const columnsCount = element?.header?.columns.length;

        const newRow = Array.from({ length: columnsCount }).map((_, columnIndex) => ({
            content: '',
            rowIndex: nextRowIndex,
            columnIndex,
            style: {},
        }));

        const updatedCells = [...(element?.body?.cells || []), ...newRow];

        setElement({
            ...element,
            body: { ...element.body, cells: updatedCells },
        });
    };

    const handleUpdateHeaderCell = (colIndex: number, value: string): void => {
        const updatedHeader = [...(element?.header?.columns || [])];
        updatedHeader[colIndex].label = value;
        setElement({ ...element, header: { ...element.header, columns: updatedHeader } });
    };

    const handleUpdateBodyCell = (rowIndex: number, colIndex: number, value: string): void => {
        const updatedCells = element?.body?.cells.map((cell: IGenericRecord) => {
            if (cell.rowIndex === rowIndex && cell.columnIndex === colIndex) {
                return { ...cell, content: value };
            }
            return cell;
        });
        setElement({ ...element, body: { ...element.body, cells: updatedCells } });
    };

    const updateSelectedCellStyle = (styleUpdate: Partial<IGenericRecord['style']>): void => {
        if (!selectedCell) return;

        if (selectedCell.row === -1) {
            const updatedColumns = element?.header?.columns.map((col: IGenericRecord, index: number) => {
                if (index === selectedCell.column) {
                    const newStyle = {
                        ...col.style,
                        ...styleUpdate,
                    };
                    setSelectedCell({ ...selectedCell, style: newStyle });
                    return { ...col, style: newStyle };
                }
                return col;
            });

            setElement({
                ...element,
                header: {
                    ...element.header,
                    columns: updatedColumns,
                },
            });
        } else {
            const updatedCells = element?.body?.cells.map((cell: IGenericRecord) => {
                if (cell.rowIndex === selectedCell.row && cell.columnIndex === selectedCell.column) {
                    const newStyle = {
                        ...cell.style,
                        ...styleUpdate,
                    };
                    setSelectedCell({ ...selectedCell, style: newStyle });
                    return { ...cell, style: newStyle };
                }
                return cell;
            });

            setElement({
                ...element,
                body: {
                    ...element.body,
                    cells: updatedCells,
                },
            });
        }
    };

    const handleMergeCells = (): void => {
        if (selectedCells.length === 0) return;

        const isHeader = selectedCells[0].row === -1;

        if (selectedCells.length === 1 && selectedCells[0].colSpan > 1) {
            const targetCell = selectedCells[0];
            const colSpan = targetCell.colSpan;
            const baseColumn = targetCell.column;

            if (isHeader) {
                const updatedColumns = element?.header?.columns.flatMap((col: IGenericRecord, index: number) => {
                    if (index === baseColumn) {
                        return Array.from({ length: colSpan }).map(() => ({
                            ...col,
                            label: '',
                            colSpan: 1,
                            style: col.style || {},
                        }));
                    }
                    return col;
                });

                setElement({
                    ...element,
                    header: {
                        ...element.header,
                        columns: updatedColumns,
                    },
                });
            } else {
                const targetRow = targetCell.row;

                const newCells = Array.from({ length: colSpan }).map((_, i) => ({
                    rowIndex: targetRow,
                    columnIndex: baseColumn + i,
                    content: '',
                    style: targetCell.style || {},
                    colSpan: 1,
                }));

                const updatedCells = element?.body?.cells.filter(
                    (cell: IGenericRecord) => cell.rowIndex !== targetRow || cell.columnIndex !== baseColumn
                );

                setElement({
                    ...element,
                    body: {
                        ...element.body,
                        cells: [...updatedCells, ...newCells],
                    },
                });
            }

            setSelectedCells([]);
            return;
        }

        if (selectedCells.length < 2) return;

        const sameRow = selectedCells.every(cell => cell.row === selectedCells[0].row);
        if (!sameRow) return;

        const sortedCells = [...selectedCells].sort((a, b) => a.column - b.column);
        const baseColumn = sortedCells[0].column;
        let colSpan = 0;
        sortedCells.forEach(sortedCell => {
            colSpan += sortedCell.colSpan || 1;
        });

        const mergedContent = isHeader
            ? sortedCells.map(cell => element?.header?.columns[cell.column].label).join(' ')
            : sortedCells
                  .map(cell => {
                      const match = element?.body?.cells.find(
                          (c: IGenericRecord) => c.rowIndex === cell.row && c.columnIndex === cell.column
                      );
                      return match?.content || '';
                  })
                  .join(' ');

        const mergedStyle = sortedCells.reduce((acc, cell) => ({ ...acc, ...cell.style }), {});

        if (isHeader) {
            const updatedColumns = element?.header?.columns
                .filter(
                    (_: IGenericRecord, index: number) =>
                        !sortedCells.some(cell => cell.column !== baseColumn && cell.column === index)
                )
                .map((col: IGenericRecord, index: number) => {
                    if (index === baseColumn) {
                        return {
                            ...col,
                            label: mergedContent,
                            colSpan,
                            style: mergedStyle,
                        };
                    }
                    return col;
                });

            setElement({
                ...element,
                header: {
                    ...element.header,
                    columns: updatedColumns,
                },
            });
            setSelectedCells([{ row: -1, column: baseColumn, style: mergedStyle, colSpan }]);
        } else {
            const targetRow = sortedCells[0].row;
            const updatedCells = element?.body?.cells
                .filter(
                    (cell: IGenericRecord) =>
                        cell.rowIndex !== targetRow ||
                        sortedCells.some(c => c.column === cell.columnIndex) === false ||
                        cell.columnIndex === baseColumn
                )
                .map((cell: IGenericRecord) => {
                    if (cell.rowIndex === targetRow && cell.columnIndex === baseColumn) {
                        return {
                            ...cell,
                            content: mergedContent,
                            style: mergedStyle,
                            colSpan,
                        };
                    }
                    return cell;
                });

            setElement({
                ...element,
                body: {
                    ...element.body,
                    cells: updatedCells,
                },
            });

            setSelectedCells([{ row: targetRow, column: baseColumn, style: mergedStyle, colSpan }]);
        }
    };

    return {
        handleAddColumn,
        handleAddRow,
        handleUpdateHeaderCell,
        handleUpdateBodyCell,
        updateSelectedCellStyle,
        handleMergeCells,
    };
};
