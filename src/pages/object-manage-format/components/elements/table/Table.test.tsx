import { render, screen, act } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { Table } from './Table';
import { ManageObjectContext, initialElementState } from '@pages/object-manage-format/context';
import type { ContextType } from 'react';
import * as TableOperationsHook from '@hooks/useTableOperations';
import { IObjectElement } from '..';

import { Header, Toolbar } from '.';
import { ObjectType } from '@constants/ObjectsEditor';

jest.mock('.', () => ({
    ...jest.requireActual('.'),
    Header: jest.fn((props) => <thead data-testid="mock-header" {...props} />),
    Body: jest.fn(() => <tbody data-testid="mock-body" />),
    Toolbar: jest.fn(() => <div data-testid="mock-toolbar" />),
}));

jest.mock('@components/icon', () => ({
    Icon: jest.fn(({ onClick }) => <button data-testid="mock-icon" onClick={onClick} />),
}));

const mockUseTableOperations = jest.spyOn(TableOperationsHook, 'useTableOperations');
const mockAddColumn = jest.fn();
const mockAddRow = jest.fn();

const mockSetElement = jest.fn();
const mockContextValue: ContextType<typeof ManageObjectContext> = {
    setElement: mockSetElement,
    element: initialElementState,
    handleClickElement: jest.fn(),
    selectedElementType: null,
    updateElementProperties: jest.fn(),
    updateElementStyles: jest.fn(),
    setSelectedElementType: jest.fn(),
};

const baseElement: IObjectElement['element'] = {
    productId: 'prod-1', name: 'Test Table', identifier: 'table-test-1', objectType: ObjectType.Table,
    id: 'table1', type: 'TABLE',
    header: { columns: [{ id: 'h1', value: 'Header' }] },
    body: { cells: [{ id: 'c1', rowIndex: 0, columnIndex: 0, value: 'Cell' }] },
    style: {},
};

const renderTable = (props: Partial<IObjectElement> = {}): ReturnType<typeof render> => {
    const contextValue = { ...mockContextValue, element: props.element || baseElement };
    return render(
        <ManageObjectContext.Provider value={contextValue}>
            <Table element={props.element || baseElement} isPreviewMode={props.isPreviewMode ?? false} />
        </ManageObjectContext.Provider>
    );
};

describe('Table Component', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        mockUseTableOperations.mockReturnValue({
            handleAddColumn: mockAddColumn,
            handleAddRow: mockAddRow,
            handleUpdateHeaderCell: jest.fn(),
            handleUpdateBodyCell: jest.fn(),
            updateSelectedCellStyle: jest.fn(),
            handleMergeCells: jest.fn(),
        });
    });

    it('debería llamar a setElement 2 veces si el "element" prop no tiene body o header', () => {
        const incompleteElement: IObjectElement['element'] = { 
            id: 'table-incomplete', type: 'TABLE',
            productId: 'prod-1', name: 'Incomplete Table', identifier: 'table-incomplete-1', objectType: ObjectType.Table,
        };
        renderTable({ element: incompleteElement });
        expect(mockSetElement).toHaveBeenCalledTimes(2);
    });

    it('debería mostrar el Toolbar cuando se selecciona una celda', () => {
        renderTable();
        expect(screen.queryByTestId('mock-toolbar')).not.toBeInTheDocument();

        const headerProps = (Header as jest.Mock).mock.calls[0][0];
        expect(headerProps.handleCellClick).toBeInstanceOf(Function);

        act(() => {
            headerProps.handleCellClick(0, 0, {});
        });

        expect(screen.getByTestId('mock-toolbar')).toBeInTheDocument();
    });

    it('no debería mostrar Toolbar ni iconos en modo previsualización', () => {
        renderTable({ isPreviewMode: true });
        expect(screen.queryByTestId('mock-toolbar')).not.toBeInTheDocument();
        expect(screen.queryByTestId('mock-icon')).not.toBeInTheDocument();
    });

    it('debería mostrar iconos de añadir si no está en modo previsualización', () => {
        renderTable({ isPreviewMode: false });
        expect(screen.getAllByTestId('mock-icon')).toHaveLength(2);
    });

    it('debería llamar a handleAddColumn y handleAddRow al hacer clic en los iconos', async () => {
        const user = userEvent.setup();
        renderTable({ isPreviewMode: false });
        const icons = screen.getAllByTestId('mock-icon');
        await user.click(icons[0]);
        await user.click(icons[1]);
        expect(mockAddColumn).toHaveBeenCalledTimes(1);
        expect(mockAddRow).toHaveBeenCalledTimes(1);
    });

    it('Toolbar recibe props correctas', () => {
        renderTable();
        const headerProps = (Header as jest.Mock).mock.calls[0][0];
        act(() => {
            headerProps.handleCellClick(0, 0, {});
        });
        const toolbarProps = (Toolbar as jest.Mock).mock.calls[0][0];
        expect(toolbarProps.selectedCell).toEqual(expect.objectContaining({ row: 0, column: 0 }));
        expect(typeof toolbarProps.updateSelectedCellStyle).toBe('function');
        expect(typeof toolbarProps.handleMergeCells).toBe('function');
    });
});
