import { IGenericRecord } from '@models/GenericRecord';

export { Table } from './Table';
export { Toolbar } from './Toolbar';
export { Header } from './Header';
export { Body } from './Body';

/**
 * Base structure for a table cell position with style.
 *
 * @typeParam row: number - Row index of the cell
 * @typeParam column: number - Column index of the cell
 * @typeParam style: IGenericRecord - Style applied to the cell
 */
export interface ISelectableCell {
    row: number;
    column: number;
    style: IGenericRecord;
}

/**
 * This describes a cell that is part of a table selection, including its column span.
 *
 * @typeParam colSpan: number - Number of columns that this cell spans
 */
export interface ISelectableCells extends ISelectableCell {
    colSpan: number;
}

/**
 * Defines the shape of a table cell selection, including position, style, and column span.
 *
 * @typeParam row: number - Row index of the selected cell
 * @typeParam column: number - Column index of the selected cell
 * @typeParam style: IGenericRecord - Style object applied to the cell
 * @typeParam colSpan: number - Number of columns spanned by the cell
 */
export interface ISelectedCell {
    row: number;
    column: number;
    style: IGenericRecord;
    colSpan: number;
}

/**
 * Represents a single table cell position.
 *
 * @typeParam row: number - Row index
 * @typeParam column: number - Column index
 */
export interface ICellPosition {
    row: number;
    column: number;
}

/**
 * Common props shared across table and cell components to handle selection and interactions.
 *
 * @typeParam selectedCells: ISelectedCell[] - Array of currently selected cells
 * @typeParam draggingCell: ICellPosition | null - Currently dragged cell
 * @typeParam setDraggingCell: function - Updates the currently dragged cell
 * @typeParam setSelectedCells: function - Updates the selected cell array
 * @typeParam getHorizontalSelection: function - Calculates horizontal range selection
 * @typeParam handleCellClick: function - Callback when a cell is clicked
 */
interface ISelectableCellProps {
    selectedCells: ISelectedCell[];
    draggingCell: ICellPosition | null;
    setDraggingCell: (cell: ICellPosition | null) => void;
    setSelectedCells: (cells: ISelectedCell[]) => void;
    getHorizontalSelection: (start: ICellPosition, end: ICellPosition) => ISelectedCell[];
    handleCellClick: (rowIndex: number, columnIndex: number, style: IGenericRecord) => void;
}

/**
 * Props for rendering the table body.
 *
 * @typeParam rows: IGenericRecord[][] - 2D array representing table rows and cells
 * @typeParam handleUpdateBodyCell: function - Updates the content of a body cell
 */
export interface TableBodyProps extends ISelectableCellProps {
    rows: IGenericRecord[][];
    handleUpdateBodyCell: (rowIndex: number, colIndex: number, value: string) => void;
}

/**
 * Props for rendering an individual body cell.
 *
 * @typeParam cell: IGenericRecord - Cell content and style
 */
export interface BodyCellProps extends ISelectableCellProps {
    cell: IGenericRecord;
    handleUpdateBodyCell: (rowIndex: number, colIndex: number, value: string) => void;
}

/**
 * Props for rendering the table header.
 *
 * @typeParam columns: IGenericRecord[] - Array of header column data
 * @typeParam globalStyles: object - Shared styles applied to the header
 * @typeParam handleUpdateHeaderCell: function - Updates the content of a header cell
 */
export interface TableHeaderProps extends ISelectableCellProps {
    columns: IGenericRecord[];
    globalStyles: { borderBottom: string };
    handleUpdateHeaderCell: (colIndex: number, value: string) => void;
}

/**
 * Props for rendering an individual header cell.
 *
 * @typeParam col: IGenericRecord - Column data and style
 * @typeParam colIndex: number - Index of the column in the header
 * @typeParam isMultiSelected: boolean - Indicates whether the cell is part of a multi-selection
 */
export interface HeaderCellProps extends ISelectableCellProps {
    col: IGenericRecord;
    colIndex: number;
    isMultiSelected: boolean;
    handleUpdateHeaderCell: (colIndex: number, value: string) => void;
}

/**
 * Props for rendering the table formatting toolbar.
 *
 * @typeParam selectedCell: object - Currently selected cell and its style
 * @typeParam updateSelectedCellStyle: function - Updates styles on the selected cell
 * @typeParam handleMergeCells: function - Merges selected cells
 */
export interface TableToolbarProps {
    selectedCell: { row: number; column: number; style: IGenericRecord } | null;
    updateSelectedCellStyle: (styleUpdate: Partial<IGenericRecord['style']>) => void;
    handleMergeCells: () => void;
}

/**
 * Initial structure of a table element within the editor.
 * Defines default content, positioning, and styles for both header and body sections of a 3x3 table.
 */
export const INITIAL_STATE_TABLE = {
    header: {
        columns: Array.from({ length: 1 }).map((_, columnIndex) => ({
            content: '',
            rowIndex: 0,
            columnIndex,
            style: {
                textAlign: 'center',
            },
        })),
        globalStyles: {
            borderBottom: '',
        },
    },
    body: {
        cells: Array.from({ length: 0 }).flatMap((_, rowIndex) =>
            Array.from({ length: 0 }).map((_, columnIndex) => ({
                content: '',
                rowIndex,
                columnIndex,
                style: {
                    textAlign: 'center',
                },
            }))
        ),
        globalStyles: {
            borderBottom: '',
        },
    },
    fixed: true,
};

/**
 * CSS styles for the icon table.
 * Defines the table's icon position and size.
 */
export const POSITION_STYLES = {
    right: 'absolute bg-[#000000] top-1/2 right-[-30px] transform -translate-y-1/2 rounded-full',
    bottom: 'absolute bottom-[-30px] left-1/2 transform -translate-x-1/2 bg-[#000000] rounded-full',
};

/**
 * Constant representing the first cell in a table.
 */
export const FIRST_CELL = -1;
