import { BodyCellProps, TableBodyProps } from '.';

export const Body: React.FC<TableBodyProps> = ({
    rows,
    selectedCells,
    draggingCell,
    setDraggingCell,
    setSelectedCells,
    getHorizontalSelection,
    handleCellClick,
    handleUpdateBodyCell,
}) => {
    return (
        <tbody>
            {rows.map((row, rowIndex) => (
                <tr key={rowIndex}>
                    {row.map((cell, colIndex) => (
                        <BodyCell
                            key={colIndex}
                            cell={cell}
                            selectedCells={selectedCells}
                            draggingCell={draggingCell}
                            setDraggingCell={setDraggingCell}
                            setSelectedCells={setSelectedCells}
                            getHorizontalSelection={getHorizontalSelection}
                            handleCellClick={handleCellClick}
                            handleUpdateBodyCell={handleUpdateBodyCell}
                        />
                    ))}
                </tr>
            ))}
        </tbody>
    );
};

const BodyCell: React.FC<BodyCellProps> = ({
    cell,
    selectedCells,
    draggingCell,
    setDraggingCell,
    setSelectedCells,
    getHorizontalSelection,
    handleCellClick,
    handleUpdateBodyCell,
}) => {
    const isMultiSelected = selectedCells.some(
        selectedCell => selectedCell.row === cell.rowIndex && selectedCell.column === cell.columnIndex
    );

    return (
        <td
            colSpan={cell.colSpan || 1}
            onMouseDown={() => setDraggingCell({ row: cell.rowIndex, column: cell.columnIndex })}
            onMouseEnter={() => {
                if (draggingCell && draggingCell.row === cell.rowIndex) {
                    const newSelection = getHorizontalSelection(draggingCell, {
                        row: cell.rowIndex,
                        column: cell.columnIndex,
                    });
                    setSelectedCells(newSelection);
                }
            }}
            onMouseUp={() => setDraggingCell(null)}
            onClick={() => handleCellClick(cell.rowIndex, cell.columnIndex, cell.style)}
            className={`border ${isMultiSelected ? 'border-red border-[0.125rem]' : 'border-[#000]'} w-[8.3333rem] h-[2.3125rem]`}
        >
            <input
                style={{ ...cell.style }}
                type="text"
                value={cell.content}
                onFocus={() => handleCellClick(cell.rowIndex, cell.columnIndex, cell.style)}
                onChange={e => handleUpdateBodyCell(cell.rowIndex, cell.columnIndex, e.target.value)}
                className="w-full h-full pl-1 bg-transparent outline-none"
            />
        </td>
    );
};
