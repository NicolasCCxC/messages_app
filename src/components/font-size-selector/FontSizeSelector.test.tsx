jest.unmock('@components/font-size-selector');

import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { FontSizeSelector } from './FontSizeSelector';
import { IOption } from '.';

const mockOptions: IOption[] = [
    { id: '1', value: 10, label: '10px' },
    { id: '2', value: 12, label: '12px' },
    { id: '3', value: 16, label: '16px' },
];

const mockOnChange = jest.fn();

describe('FontSizeSelector Component', () => {
    beforeEach(() => {
        mockOnChange.mockClear();
    });

    it('debería renderizar el placeholder cuando el valor es un string vacío', () => {
        render(
            <FontSizeSelector
                label="Font Size"
                placeholder="Select a size"
                options={mockOptions}
                onChangeOption={mockOnChange}
                value=""
            />
        );
        expect(screen.getByText('Select a size')).toBeInTheDocument();
    });

    it('debería mostrar el label del valor seleccionado', () => {
        render(
            <FontSizeSelector
                label="Font Size"
                options={mockOptions}
                value="2"
                onChangeOption={mockOnChange}
            />
        );
        expect(screen.getByText('12px')).toBeInTheDocument();
    });

    it('debería abrir y cerrar el menú desplegable al hacer clic', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector options={mockOptions} onChangeOption={mockOnChange} value="" />);
        const combobox = screen.getByRole('button');
        
        await user.click(combobox);
        expect(screen.getByPlaceholderText('Buscar...')).toBeInTheDocument();
        
        await user.click(combobox);
        expect(screen.queryByPlaceholderText('Buscar...')).not.toBeInTheDocument();
    });

    it('debería llamar a onChangeOption con la opción seleccionada al hacer clic en un item', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector name="fontSize" options={mockOptions} onChangeOption={mockOnChange} value="" />);
        await user.click(screen.getByRole('button'));
        
        await user.click(screen.getByText('16px'));
        
        expect(mockOnChange).toHaveBeenCalledWith(mockOptions[2], 'fontSize');
    });

    it('debería filtrar las opciones al escribir en el input de búsqueda', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector options={mockOptions} onChangeOption={mockOnChange} value="" />);
        await user.click(screen.getByRole('button'));
        const searchInput = screen.getByPlaceholderText('Buscar...');

        await user.type(searchInput, '12');

        expect(screen.getByText('12px')).toBeInTheDocument();
        expect(screen.queryByText('10px')).not.toBeInTheDocument();
    });

    it('debería crear y seleccionar una nueva opción numérica si no existe', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector name="fontSize" options={mockOptions} onChangeOption={mockOnChange} value="" />);
        await user.click(screen.getByRole('button'));
        const searchInput = screen.getByPlaceholderText('Buscar...');
        
        await user.type(searchInput, '20');
        await user.keyboard('{Enter}');
        
        expect(mockOnChange).toHaveBeenCalledWith({ value: 20, label: '20' }, 'fontSize');
    });

    it('debería seleccionar una opción existente si el valor custom coincide con una', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector name="fontSize" options={mockOptions} onChangeOption={mockOnChange} value="" />);
        await user.click(screen.getByRole('button'));
        const searchInput = screen.getByPlaceholderText('Buscar...');
        
        await user.type(searchInput, '12PX');
        await user.keyboard('{Enter}');

        expect(mockOnChange).toHaveBeenCalledWith(mockOptions[1], 'fontSize');
    });
});