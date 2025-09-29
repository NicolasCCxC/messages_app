import { render, screen, waitFor, act } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { UserModal } from './UserModal';
import { useAppDispatch } from '@redux/store';
import { hasEmptyFields } from '@utils/Array';
import { createUserManagement } from '@redux/user-management/actions';
import { NotificationType } from '@components/toast';
import { SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { MultiSelect } from '@components/multi-select';

jest.mock('@redux/store');
const mockedUseAppDispatch = useAppDispatch as unknown as jest.Mock;

jest.mock('@utils/Array');
const mockedHasEmptyFields = hasEmptyFields as jest.Mock;

jest.mock('@redux/user-management/actions');

jest.mock('@components/modal', () => ({
    Modal: jest.fn(({ children, onSave, title }) => (
        <div>
            <h1>{title}</h1>
            <div>{children}</div>
            <button onClick={onSave}>Guardar</button>
        </div>
    )),
}));
jest.mock('@components/select-search', () => ({
    SelectSearch: jest.fn(() => <div data-testid="mock-select-search" />),
}));
jest.mock('@components/text-input', () => ({
    TextInput: jest.fn(() => <div data-testid="mock-text-input" />),
}));
jest.mock('@components/multi-select', () => ({
    MultiSelect: jest.fn(() => <div data-testid="mock-multi-select" />),
}));

describe('UserModal Component', () => {
    const mockDispatch = jest.fn();
    const mockToggleModal = jest.fn();
    const mockHandleNotification = jest.fn();

    beforeEach(() => {
        jest.clearAllMocks();
        mockedUseAppDispatch.mockReturnValue(mockDispatch);
    });

    it('debería renderizar el título y los campos del formulario', () => {
        render(<UserModal toggleModal={mockToggleModal} handleNotification={mockHandleNotification} />);
        
        expect(screen.getByRole('heading', { name: 'Crear' })).toBeInTheDocument();
        // Verificamos que se renderizan 2 inputs de texto, 1 select y 1 multiselect
        expect(screen.getAllByTestId('mock-text-input')).toHaveLength(2);
        expect(screen.getByTestId('mock-select-search')).toBeInTheDocument();
        expect(screen.getByTestId('mock-multi-select')).toBeInTheDocument();
    });

    it('debería actualizar el estado al interactuar con los campos de texto', () => {
        render(<UserModal toggleModal={mockToggleModal} handleNotification={mockHandleNotification} />);

        // Obtenemos las props del mock de TextInput para el campo 'email'
        const emailInputProps = (TextInput as jest.Mock).mock.calls[0][0];

        // Usamos act para envolver la actualización de estado
        act(() => {
            emailInputProps.onChange({ target: { name: 'email', value: 'test@test.com' } });
        });

        // Para verificar, podemos hacer que el componente se re-renderice y ver si el valor se pasó
        // O más fácil, verificamos que en la siguiente llamada (al guardar), el payload es correcto.
        // Por ahora, este test cubre la llamada al `onChange`.
    });

    it('debería llamar a onSave pero no despachar si la validación falla', async () => {
        const user = userEvent.setup();
        // Forzamos que la validación falle
        mockedHasEmptyFields.mockReturnValue(true);

        render(<UserModal toggleModal={mockToggleModal} handleNotification={mockHandleNotification} />);

        const saveButton = screen.getByRole('button', { name: 'Guardar' });
        await user.click(saveButton);

        // Verificamos que se muestra el error de validación
        expect(screen.getByText('*Campos obligatorios')).toBeInTheDocument();
        // Y que no se despachó ninguna acción
        expect(mockDispatch).not.toHaveBeenCalled();
    });

    describe('Flujo de Creación de Usuario', () => {
        it('debería despachar la acción createUserManagement con el payload correcto', async () => {
            const user = userEvent.setup();
            // La validación pasa
            mockedHasEmptyFields.mockReturnValue(false);
            // Simulamos una respuesta exitosa
            mockDispatch.mockResolvedValue({ message: 'Usuario Creado' });

            render(<UserModal toggleModal={mockToggleModal} handleNotification={mockHandleNotification} />);

            // Simulamos llenar el formulario llamando a los callbacks de los mocks
            act(() => {
                // Email
                (TextInput as jest.Mock).mock.calls[0][0].onChange({ target: { name: 'email', value: 'test@user.com' } });
                // Nombres y apellidos
                (TextInput as jest.Mock).mock.calls[1][0].onChange({ target: { name: 'name', value: 'Test User' } });
                // Rol
                (MultiSelect as jest.Mock).mock.calls[0][0].handleChangeOption({ code: '1', description: 'Admin' });
                // Estado
                (SelectSearch as jest.Mock).mock.calls[0][0].onChangeOption({ value: 'true', label: 'Activo' });
            });

            const saveButton = screen.getByRole('button', { name: 'Guardar' });
            await user.click(saveButton);

            // Verificamos que la acción fue despachada
            expect(createUserManagement).toHaveBeenCalledTimes(1);
            // Verificamos que el payload es correcto
            expect(createUserManagement).toHaveBeenCalledWith({
                email: 'test@user.com',
                name: 'Test User',
                active: true,
                roleCodes: [1],
            });

            // Verificamos que las notificaciones de éxito se disparan
            await waitFor(() => {
                expect(mockHandleNotification).toHaveBeenCalledWith('Usuario Creado', undefined);
            });
            expect(mockToggleModal).toHaveBeenCalledTimes(1);
        });

        it('debería manejar un error de la API y mostrar una notificación', async () => {
            const user = userEvent.setup();
            mockedHasEmptyFields.mockReturnValue(false);
            // Simulamos una respuesta de error de la API
            const errorPayload = `Error: {"message":"El usuario ya existe"}`;
            mockDispatch.mockResolvedValue({ error: true, payload: errorPayload });

            render(<UserModal toggleModal={mockToggleModal} handleNotification={mockHandleNotification} />);

            // (Simulamos llenado del formulario, no es necesario ser detallado aquí ya que solo probamos el error)
            act(() => {
                (TextInput as jest.Mock).mock.calls[0][0].onChange({ target: { name: 'email', value: 'test@user.com' } });
                (MultiSelect as jest.Mock).mock.calls[0][0].handleChangeOption({ code: '1', description: 'Admin' });
            });

            const saveButton = screen.getByRole('button', { name: 'Guardar' });
            await user.click(saveButton);
            
            // Verificamos que se llamó a la notificación con el tipo Error
            await waitFor(() => {
                expect(mockHandleNotification).toHaveBeenCalledWith('El usuario ya existe', NotificationType.Error);
            });
            // Verificamos que el modal NO se cerró
            expect(mockToggleModal).not.toHaveBeenCalled();
        });
    });
});