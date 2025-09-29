import { useContext, useEffect, useMemo, useRef, useState } from 'react';
import { useTableSelection } from '@hooks/useTableSelection';
import { useTableOperations } from '@hooks/useTableOperations';
import { Icon } from '@components/icon';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import type { IGenericRecord } from '@models/GenericRecord';
import { INITIAL_STATE_TABLE, Body, Header, Toolbar, ISelectableCell, ISelectableCells, POSITION_STYLES, ICellPosition } from '.';
import { IObjectElement } from '..';

export const Table: React.FC<IObjectElement> = ({ element, isPreviewMode }) => {
    const { setElement } = useContext(ManageObjectContext);
    const [selectedCell, setSelectedCell] = useState<ISelectableCell | null>(null);
    const [selectedCells, setSelectedCells] = useState<ISelectableCells[]>([]);
    const [draggingCell, setDraggingCell] = useState<ICellPosition | null>(null);

    const divRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!element?.body || !element?.header) {
            setElement({ ...element, ...INITIAL_STATE_TABLE });
        }
    }, [element, setElement]);

    useEffect(() => {
        if (divRef.current) {
            const { height, width } = divRef.current.getBoundingClientRect();
            setElement({ ...element, style: { ...element.style, width, height } });
        }
    }, [element.body, element.header]);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent): void => {
            if (divRef.current && !divRef.current.contains(event.target as Node)) {
                setSelectedCells([]);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return (): void => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const rows = useMemo((): IGenericRecord[][] => {
        const maxRow = Math.max(...(element?.body?.cells.map((c: IGenericRecord) => c.rowIndex) || []));
        const rows: IGenericRecord[][] = [];
        for (let i = 0; i <= maxRow; i++) {
            rows.push(
                element?.body?.cells
                    .filter((cell: IGenericRecord) => cell.rowIndex === i)
                    .sort((a: IGenericRecord, b: IGenericRecord) => a.columnIndex - b.columnIndex)
            );
        }
        return rows;
    }, [element?.body?.cells]);

    const {
        handleAddColumn,
        handleAddRow,
        handleUpdateHeaderCell,
        handleUpdateBodyCell,
        updateSelectedCellStyle,
        handleMergeCells,
    } = useTableOperations({
        element,
        setElement,
        selectedCell,
        setSelectedCell,
        selectedCells,
        setSelectedCells,
        rows,
    });

    const { getHorizontalSelection } = useTableSelection({ element });

    const handleCellClick = (rowIndex: number, columnIndex: number, style: IGenericRecord): void => {
        const isDifferentCell = selectedCell?.row !== rowIndex || selectedCell?.column !== columnIndex;

        if (isDifferentCell) {
            setSelectedCells([]);
            setSelectedCell({ row: rowIndex, column: columnIndex, style });
        }
    };

    return (
        <div ref={divRef} className={isPreviewMode ? 'overflow-auto w-full h-full' : 'ml-[2.375rem]'}>
            {selectedCell && (
                <Toolbar
                    selectedCell={selectedCell}
                    updateSelectedCellStyle={updateSelectedCellStyle}
                    handleMergeCells={handleMergeCells}
                />
            )}
            <div className={`relative w-max ${isPreviewMode ? 'pointer-events-none' : ''}`}>
                <table>
                    <Header
                        columns={element?.header?.columns || []}
                        globalStyles={element?.header?.globalStyles}
                        selectedCells={selectedCells}
                        draggingCell={draggingCell}
                        setDraggingCell={setDraggingCell}
                        setSelectedCells={setSelectedCells}
                        getHorizontalSelection={getHorizontalSelection}
                        handleCellClick={handleCellClick}
                        handleUpdateHeaderCell={handleUpdateHeaderCell}
                    />
                    <Body
                        rows={rows}
                        selectedCells={selectedCells}
                        draggingCell={draggingCell}
                        setDraggingCell={setDraggingCell}
                        setSelectedCells={setSelectedCells}
                        getHorizontalSelection={getHorizontalSelection}
                        handleCellClick={handleCellClick}
                        handleUpdateBodyCell={handleUpdateBodyCell}
                    />
                </table>
                {!isPreviewMode && (
                    <>
                        <Icon name="plusWhite" onClick={handleAddColumn} className={POSITION_STYLES.right} />
                        <Icon name="plusWhite" onClick={handleAddRow} className={POSITION_STYLES.bottom} />
                    </>
                )}
            </div>
        </div>
    );
};
