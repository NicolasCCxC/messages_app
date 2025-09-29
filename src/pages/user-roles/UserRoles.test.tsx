import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router-dom';
import UserRoles from './UserRoles';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { useTableData } from '@hooks/useTableData';
import { Table } from '@components/table';

jest.mock('@redux/store');
const mockedUseAppDispatch = useAppDispatch as unknown as jest.Mock;
const mockedUseAppSelector = useAppSelector as unknown as jest.Mock;

jest.mock('@hooks/useTableData');
const mockedUseTableData = useTableData as jest.Mock;

jest.mock('@redux/user-roles/actions');

jest.mock('@components/text-input', () => ({
    TextInput: jest.fn(({ placeholder, onChange }) => (
        <input placeholder={placeholder} onChange={onChange} />
    )),
}));
jest.mock('@components/table', () => ({
    Table: jest.fn(() => <div data-testid="mock-table" />),
}));

jest.mock('@components/toast', () => {
    const originalModule = jest.requireActual('@components/toast');
    return {
        __esModule: true,
        ...originalModule,
        Toast: jest.fn(({ message }) => <div data-testid="mock-toast">{message}</div>),
    };
});


describe('UserRoles Page', () => {
    const mockDispatch = jest.fn();
    const mockRoles = [{ id: 'role-1', description: 'Admin', active: true }];
    
    const mockReduxState = {
        roles: { allData: mockRoles, pages: 1 },
        auth: { user: {} },
    };

    const renderComponent = () => {
        return render(
            <MemoryRouter>
                <UserRoles />
            </MemoryRouter>
        );
    };

    beforeEach(() => {
        jest.clearAllMocks();
        mockedUseAppDispatch.mockReturnValue(mockDispatch);
        mockedUseAppSelector.mockImplementation(selector => selector(mockReduxState));
        mockedUseTableData.mockReturnValue({
            data: mockRoles,
            onFieldChange: jest.fn(),
            updateData: jest.fn(),
        });
    });

    it('debería renderizar el título y despachar la acción inicial de carga de datos', () => {
        renderComponent();
        expect(screen.getByRole('heading', { name: 'Gestión de roles de usuario' })).toBeInTheDocument();
        expect(mockDispatch).toHaveBeenCalledTimes(1);
    });

    it('debería despachar la acción de búsqueda al hacer clic en "Consultar"', async () => {
        const user = userEvent.setup();
        renderComponent();
        const searchInput = screen.getByPlaceholderText(/Código de rol/i);
        const searchButton = screen.getByRole('button', { name: 'Consultar' });
        await user.type(searchInput, 'texto de búsqueda');
        await user.click(searchButton);
        expect(require('@redux/user-roles/actions').getUserRoles).toHaveBeenCalledWith({ search: 'texto de búsqueda' });
    });

    describe('Callback onUpdateRow', () => {
        it('debería mostrar una notificación de error si la descripción está vacía', async () => {
            mockedUseTableData.mockReturnValue({
                data: [{ id: 'role-1', description: '' }],
                onFieldChange: jest.fn(),
                updateData: jest.fn(),
            });

            renderComponent();
            const tableProps = (Table as jest.Mock).mock.calls[0][0];
            mockDispatch.mockClear();

            await tableProps.editing.onUpdateRow('role-1');

            const toast = await screen.findByTestId('mock-toast');
            expect(toast).toHaveTextContent('Campos obligatorios');
            expect(mockDispatch).not.toHaveBeenCalled();
        });

        it('debería despachar la acción "updateRole" con las propiedades modificadas', async () => {
            const editedRole = { id: 'role-1', description: 'Nuevo Admin', active: false };
            mockedUseTableData.mockReturnValue({ data: [editedRole], onFieldChange: jest.fn(), updateData: jest.fn() });
            mockDispatch.mockResolvedValue({ payload: { message: 'Rol Actualizado' } });
            
            renderComponent();
            const tableProps = (Table as jest.Mock).mock.calls[0][0];
            mockDispatch.mockClear();
            
            await tableProps.editing.onUpdateRow('role-1');

            expect(mockDispatch).toHaveBeenCalledTimes(1);
            const expectedPayload = { id: 'role-1', description: 'Nuevo Admin', active: false };
            expect(require('@redux/user-roles/actions').updateRole).toHaveBeenCalledWith(expectedPayload);
            
            const toast = await screen.findByTestId('mock-toast');
            expect(toast).toHaveTextContent('Rol Actualizado');
        });
    });
});