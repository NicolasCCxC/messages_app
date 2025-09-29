import { render, screen, waitFor, act } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { ExecutionAssistedProcessModal } from './ExecutionAssistedProcessModal';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { apiGetFormat } from '@api/ExecutionAssistedProcess';

// -- Mocks --
jest.mock('@redux/store');
const mockedUseAppDispatch = useAppDispatch as unknown as jest.Mock;
const mockedUseAppSelector = useAppSelector as unknown as jest.Mock;

jest.mock('@components/modal', () => ({
    Modal: jest.fn(({ children, onSave, title }) => (
        <div data-testid="mock-modal">
            <h1>{title}</h1>
            {children}
            <button onClick={onSave}>Guardar</button>
        </div>
    )),
}));
jest.mock('@components/select-search', () => ({
    SelectSearch: jest.fn(({ onChangeOption, label }) => (
        <div>
            <label>{label}</label>
            <button
                data-testid="mock-select-product"
                onClick={() => onChangeOption({ id: 'prod-1', value: 'prod-1', label: 'Producto 1' })}
            />
        </div>
    )),
}));
jest.mock('@components/text-input', () => ({
    TextInput: jest.fn(({ onChange, label, name, disabled, value }) => (
        <div>
            <label>{label}</label>
            <input
                data-testid={`mock-input-${name}`}
                type="text"
                value={value || ''}
                onChange={e => onChange(e)}
                disabled={disabled}
            />
        </div>
    )),
}));

// Mock de la API
jest.mock('@api/ExecutionAssistedProcess', () => ({
    apiGetFormat: jest.fn(),
}));

describe('ExecutionAssistedProcessModal Component', () => {
    const mockDispatch = jest.fn();
    const mockToggleModal = jest.fn();
    const mockToggleToast = jest.fn();
    const mockHandleMessageToast = jest.fn();
    const mockReduxState = {
        productManagement: { allProducts: [{ value: 'prod-1', label: 'Producto 1' }] },
    };

    beforeEach(() => {
        jest.clearAllMocks();
        mockedUseAppDispatch.mockReturnValue(mockDispatch);
        mockedUseAppSelector.mockImplementation(selector => selector(mockReduxState));

        // Valor por defecto de la API
        (apiGetFormat as jest.Mock).mockResolvedValue({ data: { version: '1234' } });
    });

    it('debería mostrar un error de validación si los campos requeridos están vacíos al guardar', async () => {
        const user = userEvent.setup();
        render(
            <ExecutionAssistedProcessModal
                toggleModal={mockToggleModal}
                toggleToast={mockToggleToast}
                handleMessageToast={mockHandleMessageToast}
            />
        );

        const saveButton = screen.getByRole('button', { name: 'Guardar' });
        await user.click(saveButton);

        expect(screen.getByText(`*${REQUIRED_FIELDS}`)).toBeInTheDocument();
        expect(mockDispatch).not.toHaveBeenCalled();
    });

    it('debería despachar la acción y cerrar el modal en un envío exitoso', async () => {
        const user = userEvent.setup();
        mockDispatch.mockResolvedValue({ payload: { message: 'Proceso exitoso' } });

        const { rerender } = render(
            <ExecutionAssistedProcessModal
                toggleModal={mockToggleModal}
                toggleToast={mockToggleToast}
                handleMessageToast={mockHandleMessageToast}
            />
        );

        act(() => {
            (SelectSearch as jest.Mock).mock.calls[0][0].onChangeOption({ id: 'prod-1', value: 'prod-1' });
            (TextInput as jest.Mock).mock.calls[1][0].onChange({ target: { name: 'period', value: '202508' } });
        });

        rerender(
            <ExecutionAssistedProcessModal
                toggleModal={mockToggleModal}
                toggleToast={mockToggleToast}
                handleMessageToast={mockHandleMessageToast}
            />
        );

        const saveButton = screen.getByRole('button', { name: 'Guardar' });
        await user.click(saveButton);

        expect(mockDispatch).toHaveBeenCalledWith(expect.any(Function));
        await waitFor(() => {
            expect(mockHandleMessageToast).toHaveBeenCalledWith('Proceso exitoso');
        });
        expect(mockToggleToast).toHaveBeenCalledTimes(1);
        expect(mockToggleModal).toHaveBeenCalledTimes(1);
    });

    it('debería mostrar un mensaje de error de la API si el dispatch falla', async () => {
        const user = userEvent.setup();
        const errorMessage = 'Hubo un error en el servidor';
        const errorPayload = `Error: {"message":"${errorMessage}"}`;
        mockDispatch.mockResolvedValue({ error: true, payload: errorPayload });

        const { rerender } = render(
            <ExecutionAssistedProcessModal
                toggleModal={mockToggleModal}
                toggleToast={mockToggleToast}
                handleMessageToast={mockHandleMessageToast}
            />
        );

        act(() => {
            (SelectSearch as jest.Mock).mock.calls[0][0].onChangeOption({ id: 'prod-1', value: 'prod-1' });
            (TextInput as jest.Mock).mock.calls[1][0].onChange({ target: { name: 'period', value: '202508' } });
        });

        rerender(
            <ExecutionAssistedProcessModal
                toggleModal={mockToggleModal}
                toggleToast={mockToggleToast}
                handleMessageToast={mockHandleMessageToast}
            />
        );

        const saveButton = screen.getByRole('button', { name: 'Guardar' });
        await user.click(saveButton);

        expect(mockDispatch).toHaveBeenCalled();
        expect(await screen.findByText(`*${errorMessage}`)).toBeInTheDocument();
        expect(mockHandleMessageToast).not.toHaveBeenCalled();
    });
});
