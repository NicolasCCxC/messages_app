import { render, screen } from '@testing-library/react';
import { Table } from './Table';
import { IElement } from '@pages/object-manage-format/context'; 
import { ObjectType } from '@constants/ObjectsEditor';

// --- Datos de Prueba ---
const mockElement: IElement = {
    productId: 'prod-1', name: 'Test PDF Table', identifier: 'pdf-table-1', objectType: ObjectType.Table, type: 'TABLE',
    header: {
        columns: [
            { label: 'Header 1', style: { color: 'red' } },
            { label: 'Header 2', colSpan: 2 },
        ],
    },
    body: {
        cells: [
            { rowIndex: 0, columnIndex: 0, content: 'Cell A1', style: { fontWeight: 'bold' } },
            { rowIndex: 0, columnIndex: 1, content: 'Cell B1' },
            { rowIndex: 1, columnIndex: 0, content: 'Cell A2', rowSpan: 2 },
            { rowIndex: 1, columnIndex: 1, content: 'Cell B2' },
        ],
    },
};

describe('PDF Table Component', () => {
    it('debería renderizar correctamente los encabezados y las celdas', () => {
        render(<Table element={mockElement} />);
        expect(screen.getByText('Header 1')).toBeInTheDocument();
        expect(screen.getByText('Cell A1')).toBeInTheDocument();
    });

    it('debería aplicar correctamente los atributos colSpan, rowSpan y styles', () => {
        render(<Table element={mockElement} />);
        const header2 = screen.getByText('Header 2');
        expect(header2).toHaveAttribute('colSpan', '2');

        const cellA2 = screen.getByText('Cell A2');
        expect(cellA2).toHaveAttribute('rowSpan', '2');

        const header1 = screen.getByText('Header 1');
        // --- CORRECCIÓN FINAL AQUÍ ---
        // Verificamos la propiedad 'style.color' del elemento del DOM directamente.
        expect(header1.style.color).toBe('red');
        
        const cellA1 = screen.getByText('Cell A1');
        expect(cellA1).toHaveStyle({ fontWeight: 'bold' }); // Este debería funcionar bien
    });

    it('debería renderizar sin errores si el header o el body no están definidos', () => {
        const { unmount } = render(<Table element={{ ...mockElement, header: undefined }} />);
        expect(screen.queryByText('Header 1')).not.toBeInTheDocument();
        
        unmount();
        render(<Table element={{ ...mockElement, body: undefined }} />);
        expect(screen.queryByText('Cell A1')).not.toBeInTheDocument();
    });
    
    it('debería renderizar un tbody vacío si el array de celdas del body está vacío', () => {
        const elementWithEmptyCells: IElement = {
            ...mockElement,
            body: { cells: [] },
        };
        render(<Table element={elementWithEmptyCells} />);
        const tbody = screen.getByRole('table').querySelector('tbody');
        expect(tbody).toBeInTheDocument();
        expect(tbody?.hasChildNodes()).toBe(false);
    });

    it('debería renderizar una tabla dispersa (sparse) correctamente', () => {
        const sparseElement: IElement = {
            productId: 'prod-2', name: 'Sparse Table', identifier: 'sparse-1', objectType: ObjectType.Table, type: 'TABLE',
            header: { columns: [] },
            body: {
                cells: [
                    { rowIndex: 0, columnIndex: 0, content: 'Presente' },
                    { rowIndex: 1, columnIndex: 0, content: 'Presente' },
                    { rowIndex: 1, columnIndex: 1, content: 'Presente' },
                ],
            },
        };
        render(<Table element={sparseElement} />);
        const tbody = screen.getByRole('table').querySelector('tbody');
        expect(tbody?.querySelectorAll('tr')).toHaveLength(2);

        const cells = screen.getAllByRole('cell');
        expect(cells).toHaveLength(4);
    });
});