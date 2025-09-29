import type { IGenericRecord } from '@models/GenericRecord';
import { FIRST_CELL, HeaderCellProps, TableHeaderProps } from '.';

export const Header: React.FC<TableHeaderProps> = ({
    columns,
    selectedCells,
    draggingCell,
    setDraggingCell,
    setSelectedCells,
    getHorizontalSelection,
    handleCellClick,
    handleUpdateHeaderCell,
}) => (
    <thead>
        <tr>
            {columns.map((col: IGenericRecord, colIndex: number) => {
                const isMultiSelected = selectedCells.some(
                    selectedCell => selectedCell.row === FIRST_CELL && selectedCell.column === colIndex
                );

                return (
                    <HeaderCell
                        key={col.columnIndex}
                        col={col}
                        colIndex={colIndex}
                        isMultiSelected={isMultiSelected}
                        draggingCell={draggingCell}
                        setDraggingCell={setDraggingCell}
                        setSelectedCells={setSelectedCells}
                        getHorizontalSelection={getHorizontalSelection}
                        handleCellClick={handleCellClick}
                        handleUpdateHeaderCell={handleUpdateHeaderCell}
                        selectedCells={[]}
                    />
                );
            })}
        </tr>
    </thead>
);

const HeaderCell: React.FC<HeaderCellProps> = ({
    col,
    colIndex,
    isMultiSelected,
    draggingCell,
    setDraggingCell,
    setSelectedCells,
    getHorizontalSelection,
    handleCellClick,
    handleUpdateHeaderCell,
}) => (
    <th
        colSpan={col.colSpan || 1}
        onMouseDown={() => setDraggingCell({ row: FIRST_CELL, column: colIndex })}
        onMouseEnter={() => {
            if (draggingCell) {
                setSelectedCells(
                    getHorizontalSelection(draggingCell, {
                        row: FIRST_CELL,
                        column: colIndex,
                    })
                );
            }
        }}
        onMouseUp={() => setDraggingCell(null)}
        onClick={() => handleCellClick(FIRST_CELL, colIndex, col.style)}
        className={`border w-[8.3333rem] h-[2.3125rem] text-left ${
            isMultiSelected ? 'border-red border-[2px]' : 'border-[#000]'
        } bg-gray-light`}
    >
        <input
            type="text"
            value={col.label}
            onFocus={() => handleCellClick(FIRST_CELL, colIndex, col.style)}
            onChange={e => handleUpdateHeaderCell(colIndex, e.target.value)}
            className="w-full h-full font-medium bg-transparent outline-none pxFIRST_CELL"
            style={col.style}
        />
    </th>
);
