import { render, screen, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { Body } from './Body';

const mockSetDraggingCell = jest.fn();
const mockSetSelectedCells = jest.fn();
const mockGetHorizontalSelection = jest.fn();
const mockHandleCellClick = jest.fn();
const mockHandleUpdateBodyCell = jest.fn();

const mockRows = [
    [
        { rowIndex: 0, columnIndex: 0, content: 'A1', style: {} },
        { rowIndex: 0, columnIndex: 1, content: 'B1', style: {} },
    ],
];

const renderBody = (props: any = {}) => {
    const defaultProps = {
        rows: mockRows,
        selectedCells: [],
        draggingCell: null,
        setDraggingCell: mockSetDraggingCell,
        setSelectedCells: mockSetSelectedCells,
        getHorizontalSelection: mockGetHorizontalSelection,
        handleCellClick: mockHandleCellClick,
        handleUpdateBodyCell: mockHandleUpdateBodyCell,
    };

    return render(
        <table>
            <Body {...defaultProps} {...props} />
        </table>
    );
};

describe('Table.Body (Object Manage) Component', () => {

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería renderizar el número correcto de filas y celdas', () => {
        renderBody();
        expect(screen.getAllByRole('row')).toHaveLength(1);
        expect(screen.getAllByRole('cell')).toHaveLength(2);
        expect(screen.getByDisplayValue('A1')).toBeInTheDocument();
    });

    it('debería aplicar la clase de selección si una celda está en "selectedCells"', () => {
        const selected = [{ row: 0, column: 1 }];
        renderBody({ selectedCells: selected });

        const cellB1 = screen.getByDisplayValue('B1').closest('td');
        expect(cellB1).toHaveClass('border-red');
        
        const cellA1 = screen.getByDisplayValue('A1').closest('td');
        expect(cellA1).not.toHaveClass('border-red');
    });

    it('debería llamar a handleUpdateBodyCell al escribir en un input', () => {
        renderBody();
        const inputA1 = screen.getByDisplayValue('A1');
        
        fireEvent.change(inputA1, { target: { value: 'A1 updated' } });
        
        expect(mockHandleUpdateBodyCell).toHaveBeenCalledWith(0, 0, 'A1 updated');
    });

    it('debería llamar a handleCellClick al hacer clic en una celda o al enfocar un input', async () => {
        const user = userEvent.setup();
        renderBody();

        const cellB1 = screen.getByDisplayValue('B1').closest('td')!;
        await user.click(cellB1);
        
        expect(mockHandleCellClick).toHaveBeenCalledWith(0, 1, {});

        const inputA1 = screen.getByDisplayValue('A1');
        fireEvent.focus(inputA1);
        expect(mockHandleCellClick).toHaveBeenCalledWith(0, 0, {});
    });

    describe('Lógica de Selección por Arrastre (Drag)', () => {
        it('debería llamar a setDraggingCell al presionar el mouse sobre una celda', () => {
            renderBody();
            const cellA1 = screen.getByDisplayValue('A1').closest('td')!;
            fireEvent.mouseDown(cellA1);
            expect(mockSetDraggingCell).toHaveBeenCalledWith({ row: 0, column: 0 });
        });

        it('debería llamar a getHorizontalSelection y setSelectedCells al arrastrar sobre otra celda en la misma fila', () => {
            const startCell = { row: 0, column: 0 };
            const endCell = { row: 0, column: 1 };
            const mockSelection = [startCell, endCell];
            mockGetHorizontalSelection.mockReturnValue(mockSelection);

            renderBody({ draggingCell: startCell });
            const cellB1 = screen.getByDisplayValue('B1').closest('td')!;
            fireEvent.mouseEnter(cellB1);

            expect(mockGetHorizontalSelection).toHaveBeenCalledWith(startCell, endCell);
            expect(mockSetSelectedCells).toHaveBeenCalledWith(mockSelection);
        });

        it('NO debería llamar a getHorizontalSelection si se arrastra a una fila diferente', () => {
            const twoRows = [...mockRows, [{ rowIndex: 1, columnIndex: 0, content: 'A2' }]];
            const startCell = { row: 0, column: 0 };
            renderBody({ rows: twoRows, draggingCell: startCell });
            
            const cellA2 = screen.getByDisplayValue('A2').closest('td')!;
            fireEvent.mouseEnter(cellA2);

            expect(mockGetHorizontalSelection).not.toHaveBeenCalled();
        });

        it('debería llamar a setDraggingCell con null al soltar el mouse', () => {
            renderBody();
            const cellA1 = screen.getByDisplayValue('A1').closest('td')!;
            fireEvent.mouseUp(cellA1);
            expect(mockSetDraggingCell).toHaveBeenCalledWith(null);
        });
    });
});
