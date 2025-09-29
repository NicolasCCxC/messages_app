import { render, screen, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { Header } from './Header';

// -- Configuración del Test --

// Mockeamos las funciones que el componente recibe como props
const mockSetDraggingCell = jest.fn();
const mockSetSelectedCells = jest.fn();
const mockGetHorizontalSelection = jest.fn();
const mockHandleCellClick = jest.fn();
const mockHandleUpdateHeaderCell = jest.fn();

// Asumimos que FIRST_CELL es -1 según las convenciones de tablas (header-row)
const FIRST_CELL = -1;

// Creamos datos de prueba para las columnas
const mockColumns = [
    { columnIndex: 0, label: 'Columna A', style: { color: 'blue' }, colSpan: 1 },
    { columnIndex: 1, label: 'Columna B', style: { color: 'green' }, colSpan: 1 },
];

// Función de ayuda para renderizar el componente con las props necesarias
const renderHeader = (props: any) => {
    return render(
        <table>
            <Header
                columns={mockColumns}
                selectedCells={[]}
                draggingCell={null}
                setDraggingCell={mockSetDraggingCell}
                setSelectedCells={mockSetSelectedCells}
                getHorizontalSelection={mockGetHorizontalSelection}
                handleCellClick={mockHandleCellClick}
                handleUpdateHeaderCell={mockHandleUpdateHeaderCell}
                {...props}
            />
        </table>
    );
};

describe('Table.Header (Object Manage) Component', () => {

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería renderizar el número correcto de celdas de encabezado (th)', () => {
        renderHeader({});
        // Deberíamos tener 2 celdas de encabezado (<th>)
        expect(screen.getAllByRole('columnheader')).toHaveLength(2);
        // Verificamos que el contenido está presente
        expect(screen.getByDisplayValue('Columna A')).toBeInTheDocument();
    });

    it('debería aplicar la clase de selección si una celda está en "selectedCells"', () => {
        const selected = [{ row: FIRST_CELL, column: 1 }]; // La Columna B está seleccionada
        renderHeader({ selectedCells: selected });

        const cellB = screen.getByDisplayValue('Columna B').closest('th');
        expect(cellB).toHaveClass('border-red');
        
        const cellA = screen.getByDisplayValue('Columna A').closest('th');
        expect(cellA).not.toHaveClass('border-red');
    });

    it('debería llamar a handleUpdateHeaderCell al escribir en un input', () => {
        renderHeader({});
        const inputA = screen.getByDisplayValue('Columna A');

        // Usamos fireEvent.change porque es un input controlado en un test aislado
        fireEvent.change(inputA, { target: { value: 'Nuevo Título' } });

        // Verificamos que se llamó con el índice de columna correcto (0) y el nuevo valor
        expect(mockHandleUpdateHeaderCell).toHaveBeenCalledWith(0, 'Nuevo Título');
    });

    it('debería llamar a handleCellClick al hacer clic en una celda o al enfocar un input', async () => {
        const user = userEvent.setup();
        renderHeader({});

        const cellB = screen.getByDisplayValue('Columna B').closest('th')!;
        await user.click(cellB);
        
        // Verificamos que se llamó con la fila del header (-1), el índice de la columna (1) y su estilo
        expect(mockHandleCellClick).toHaveBeenCalledWith(FIRST_CELL, 1, { color: 'green' });

        const inputA = screen.getByDisplayValue('Columna A');
        fireEvent.focus(inputA);
        expect(mockHandleCellClick).toHaveBeenCalledWith(FIRST_CELL, 0, { color: 'blue' });
    });

    describe('Lógica de Selección por Arrastre (Drag)', () => {
        it('debería llamar a setDraggingCell al presionar el mouse sobre una celda', () => {
            renderHeader({});
            const cellA = screen.getByDisplayValue('Columna A').closest('th')!;
            fireEvent.mouseDown(cellA);
            expect(mockSetDraggingCell).toHaveBeenCalledWith({ row: FIRST_CELL, column: 0 });
        });

        it('debería llamar a getHorizontalSelection y setSelectedCells al arrastrar sobre otra celda', () => {
            const startCell = { row: FIRST_CELL, column: 0 };
            const endCell = { row: FIRST_CELL, column: 1 };
            const mockSelection = [startCell, endCell];
            mockGetHorizontalSelection.mockReturnValue(mockSelection);

            renderHeader({ draggingCell: startCell });
            const cellB = screen.getByDisplayValue('Columna B').closest('th')!;
            fireEvent.mouseEnter(cellB);

            expect(mockGetHorizontalSelection).toHaveBeenCalledWith(startCell, endCell);
            expect(mockSetSelectedCells).toHaveBeenCalledWith(mockSelection);
        });

        it('NO debería llamar a la lógica de selección si no se está arrastrando (draggingCell es null)', () => {
            renderHeader({ draggingCell: null }); // draggingCell es null
            
            const cellB = screen.getByDisplayValue('Columna B').closest('th')!;
            fireEvent.mouseEnter(cellB);

            expect(mockGetHorizontalSelection).not.toHaveBeenCalled();
            expect(mockSetSelectedCells).not.toHaveBeenCalled();
        });

        it('debería llamar a setDraggingCell con null al soltar el mouse', () => {
            renderHeader({});
            const cellA = screen.getByDisplayValue('Columna A').closest('th')!;
            fireEvent.mouseUp(cellA);
            expect(mockSetDraggingCell).toHaveBeenCalledWith(null);
        });
    });
});
