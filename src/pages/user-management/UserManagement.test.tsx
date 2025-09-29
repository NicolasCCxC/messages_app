import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router-dom';
import UserManagement from './UserManagement';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { useRole } from '@hooks/useRole';
import { useTableData } from '@hooks/useTableData';
import { DefaultUserRoles } from '@constants/User';
import { getDiff } from '@utils/Diff';
import { hasEmptyFields } from '@utils/Array';
import { Table } from '@components/table';

jest.mock('@redux/store');
const mockedUseAppDispatch = useAppDispatch as unknown as jest.Mock;
const mockedUseAppSelector = useAppSelector as unknown as jest.Mock;

jest.mock('@hooks/useRole');
const mockedUseRole = useRole as jest.Mock;

jest.mock('@hooks/useTableData');
const mockedUseTableData = useTableData as jest.Mock;

jest.mock('@redux/user-management/actions');
jest.mock('@redux/user-roles/actions');

jest.mock('@components/text-input', () => ({
    TextInput: jest.fn(({ placeholder, onChange }) => (
        <input placeholder={placeholder} onChange={onChange} />
    )),
}));
jest.mock('@components/table', () => ({
    Table: jest.fn(() => <div data-testid="mock-table" />),
}));
jest.mock('.', () => ({
    ...jest.requireActual('.'),
    UserModal: jest.fn(() => <div data-testid="mock-user-modal" />),
}));
jest.mock('@components/toast', () => {
    const originalModule = jest.requireActual('@components/toast');
    return { ...originalModule, Toast: jest.fn(() => <div data-testid="mock-toast" />) };
});
jest.mock('@utils/Diff');
jest.mock('@utils/Array');

const mockedGetDiff = getDiff as jest.Mock;
const mockedHasEmptyFields = hasEmptyFields as jest.Mock;

describe('UserManagement Page', () => {
    const mockDispatch = jest.fn();
    const mockUsers = [{ id: 'user-1', userName: 'testuser', edit: false }];
    const mockRoles = [{ code: 'admin', description: 'Administrator' }];
    const mockReduxState = {
        userManagement: { users: mockUsers, data: { totalPages: 1 } },
        roles: { allData: mockRoles },
        auth: { user: {} },
    };

    const renderComponent = () => render(<MemoryRouter><UserManagement /></MemoryRouter>);

    beforeEach(() => {
        jest.clearAllMocks();
        mockedUseAppDispatch.mockReturnValue(mockDispatch);
        mockedUseAppSelector.mockImplementation(selector => selector(mockReduxState));
        mockedUseRole.mockReturnValue(DefaultUserRoles.Administrator);
        mockedUseTableData.mockReturnValue({
            data: mockUsers,
            onFieldChange: jest.fn(),
            updateData: jest.fn(),
        });
    });

    it('debería renderizar el título y despachar las acciones iniciales de carga de datos', () => {
        renderComponent();
        expect(screen.getByRole('heading', { name: 'Gestión de usuarios' })).toBeInTheDocument();
        expect(mockDispatch).toHaveBeenCalledTimes(2);
    });

    it('debería abrir el modal de "Crear" al hacer clic en el botón', async () => {
        const user = userEvent.setup();
        renderComponent();
        expect(screen.queryByTestId('mock-user-modal')).not.toBeInTheDocument();
        const createButton = screen.getByRole('button', { name: 'Crear' });
        await user.click(createButton);
        expect(screen.getByTestId('mock-user-modal')).toBeInTheDocument();
    });

    it('debería despachar la acción de búsqueda al hacer clic en "Consultar"', async () => {
        const user = userEvent.setup();
        renderComponent();
        const searchInput = screen.getByPlaceholderText(/Usuario de red/i);
        const searchButton = screen.getByRole('button', { name: 'Consultar' });
        await user.type(searchInput, 'texto de búsqueda');
        await user.click(searchButton);
        expect(require('@redux/user-management/actions').getUserManagement).toHaveBeenCalledWith({ search: 'texto de búsqueda' });
    });

    describe('Callback onUpdateRow', () => {
        it('debería mostrar un toast de error si la validación de campos vacíos falla', async () => {
            mockedHasEmptyFields.mockReturnValue(true);
            renderComponent();
            const tableProps = (Table as jest.Mock).mock.calls[0][0];
            mockDispatch.mockClear();

            await tableProps.editing.onUpdateRow('user-1');
            expect(screen.getByTestId('mock-toast')).toBeInTheDocument();
            expect(mockDispatch).not.toHaveBeenCalled();
        });

        it('debería despachar la acción "modifyUserManagement" en una actualización exitosa', async () => {
            mockedHasEmptyFields.mockReturnValue(false);
            mockedGetDiff.mockReturnValue({ roleCodes: ['new-role'] });
            mockDispatch.mockResolvedValue({ payload: { message: 'Actualizado!' } });
            
            const mockUsersInEditMode = [{ id: 'user-1', userName: 'testuser', edit: true }];
            mockedUseTableData.mockReturnValue({
                data: mockUsersInEditMode,
                onFieldChange: jest.fn(),
                updateData: jest.fn(),
            });
            
            renderComponent();
            const tableProps = (Table as jest.Mock).mock.calls[0][0];
            mockDispatch.mockClear();

            await tableProps.editing.onUpdateRow('user-1');
            
            expect(mockDispatch).toHaveBeenCalledTimes(1);
            expect(require('@redux/user-management/actions').modifyUserManagement).toHaveBeenCalledWith(expect.objectContaining({ id: 'user-1' }));
            
            await waitFor(() => {
                expect(screen.getByTestId('mock-toast')).toBeInTheDocument();
            });
        });

        it('debería mostrar un toast de error si la API falla al actualizar', async () => {
            mockedHasEmptyFields.mockReturnValue(false);
            mockedGetDiff.mockReturnValue({ name: 'nuevo nombre' });
            const errorPayload = `Error: {"message":"Error del servidor"}`;
            mockDispatch.mockResolvedValue({ error: true, payload: errorPayload });

            const mockUsersInEditMode = [{ id: 'user-1', userName: 'testuser', edit: true }];
            mockedUseTableData.mockReturnValue({
                data: mockUsersInEditMode,
                onFieldChange: jest.fn(),
                updateData: jest.fn(),
            });

            renderComponent();
            const tableProps = (Table as jest.Mock).mock.calls[0][0];
            mockDispatch.mockClear();

            await tableProps.editing.onUpdateRow('user-1');

            await waitFor(() => {
                expect(screen.getByTestId('mock-toast')).toBeInTheDocument();
            });
        });
    });
});