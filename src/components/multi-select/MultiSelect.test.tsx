import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MultiSelect } from './MultiSelect';
import { IOption } from '.';

// -- Mocks y Configuración --
jest.mock('@components/icon', () => ({
    Icon: jest.fn(() => <span data-testid="mock-icon" />),
}));

const mockOptions: IOption[] = [
    { code: 'opt1', description: 'Opción 1' },
    { code: 'opt2', description: 'Opción 2' },
    { code: 'opt3', description: 'Opción 3' },
];

const mockHandleChange = jest.fn();

describe('MultiSelect Component', () => {

    beforeEach(() => {
        mockHandleChange.mockClear();
    });

    it('debería renderizar el label y el placeholder si no hay opciones seleccionadas', () => {
        render(
            <MultiSelect
                label="Selecciona items"
                options={mockOptions}
                selectedOptions={[]}
                handleChangeOption={mockHandleChange}
            />
        );

        expect(screen.getByText('Selecciona items')).toBeInTheDocument();
        expect(screen.getByText('Seleccionar')).toBeInTheDocument();
    });

    it('debería mostrar las descripciones de las opciones seleccionadas unidas por comas', () => {
        const selected = [mockOptions[0], mockOptions[2]];
        render(
            <MultiSelect
                label="Selecciona items"
                options={mockOptions}
                selectedOptions={selected}
                handleChangeOption={mockHandleChange}
            />
        );

        expect(screen.getByText('Opción 1, Opción 3')).toBeInTheDocument();
    });

    it('debería abrir el menú desplegable al hacer clic y cerrarlo al volver a hacer clic', async () => {
        const user = userEvent.setup();
        render(<MultiSelect options={mockOptions} selectedOptions={[]} handleChangeOption={mockHandleChange} />);
        
        const dropdownButton = screen.getByText('Seleccionar');

        expect(screen.queryByText('Opción 1')).not.toBeInTheDocument();
        
        await user.click(dropdownButton);
        expect(screen.getByText('Opción 1')).toBeInTheDocument();

        await user.click(dropdownButton);
        expect(screen.queryByText('Opción 1')).not.toBeInTheDocument();
    });

    it('debería cerrar el menú al hacer clic fuera del componente', async () => {
        const user = userEvent.setup();
        render(
            <div>
                <MultiSelect options={mockOptions} selectedOptions={[]} handleChangeOption={mockHandleChange} />
                <button>Fuera</button>
            </div>
        );
        
        // --- CORRECCIÓN AQUÍ ---
        // Buscamos el botón del multiselect por su texto para ser específicos
        const multiSelectButton = screen.getByText('Seleccionar');
        
        // Abrimos el menú
        await user.click(multiSelectButton);
        expect(screen.getByText('Opción 1')).toBeInTheDocument();

        // Hacemos clic fuera
        await user.click(screen.getByText('Fuera'));
        expect(screen.queryByText('Opción 1')).not.toBeInTheDocument();
    });

    it('debería llamar a handleChangeOption al hacer clic en una opción', async () => {
        const user = userEvent.setup();
        render(<MultiSelect options={mockOptions} selectedOptions={[]} handleChangeOption={mockHandleChange} />);

        await user.click(screen.getByText('Seleccionar'));
        
        const option2Label = screen.getByText('Opción 2');
        await user.click(option2Label);

        expect(mockHandleChange).toHaveBeenCalledTimes(1);
        expect(mockHandleChange).toHaveBeenCalledWith(mockOptions[1]);
    });

    it('debería mostrar las opciones correctas como seleccionadas (checked)', async () => {
        const user = userEvent.setup();
        const selected = [mockOptions[0]];
        render(<MultiSelect options={mockOptions} selectedOptions={selected} handleChangeOption={mockHandleChange} />);
        
        await user.click(screen.getByText('Opción 1'));
        
        const checkbox1 = screen.getByLabelText('Opción 1') as HTMLInputElement;
        const checkbox2 = screen.getByLabelText('Opción 2') as HTMLInputElement;

        expect(checkbox1.checked).toBe(true);
        expect(checkbox2.checked).toBe(false);
    });
});