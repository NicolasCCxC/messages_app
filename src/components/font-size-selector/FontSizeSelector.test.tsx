jest.unmock('@components/font-size-selector');

import { render, screen, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { FontSizeSelector } from './FontSizeSelector';
import { IOption } from '.';
import { ENTER } from '@components/form';

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
    it('debería seleccionar una opción al presionar Enter cuando está enfocada', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector name="fontSize" options={mockOptions} onChangeOption={mockOnChange} value="" />);

        // Abrir el dropdown
        await user.click(screen.getByRole('button'));

        // Encontrar la opción y enfocarla
        const option = screen.getByText('16px');
        option.focus();

        // Simular presionar Enter
        await user.keyboard('{Enter}');

        // Verificar que se llamó a onChangeOption con la opción correcta
        expect(mockOnChange).toHaveBeenCalledWith(mockOptions[2], 'fontSize');
    });

    it('no debería seleccionar una opción al presionar teclas diferentes a Enter', async () => {
        const user = userEvent.setup();
        render(<FontSizeSelector name="fontSize" options={mockOptions} onChangeOption={mockOnChange} value="" />);

        // Abrir el dropdown
        await user.click(screen.getByRole('button'));

        // Encontrar la opción y enfocarla
        const option = screen.getByText('16px');
        option.focus();

        // Simular presionar Space
        await user.keyboard(' ');

        // Verificar que no se llamó a onChangeOption
        expect(mockOnChange).not.toHaveBeenCalled();

        // Simular presionar Tab
        await user.keyboard('{Tab}');

        // Verificar que no se llamó a onChangeOption
        expect(mockOnChange).not.toHaveBeenCalled();
    });

    it('debería llamar a handleSelectOption cuando se presiona Enter en el onKeyDown del elemento de la lista', () => {
        render(<FontSizeSelector name="fontSize" options={mockOptions} onChangeOption={mockOnChange} value="" />);

        // Abrir el dropdown
        fireEvent.click(screen.getByRole('button'));

        // Encontrar el elemento de la lista (li) que contiene la opción
        const listItem = screen.getByText('16px').closest('li');

        // Verificar que el elemento existe antes de simular el evento
        if (listItem) {
            // Simular directamente el evento onKeyDown con la tecla Enter
            fireEvent.keyDown(listItem, { key: ENTER });
        } else {
            throw new Error('List item element not found');
        }

        // Verificar que se llamó a onChangeOption con la opción correcta
        expect(mockOnChange).toHaveBeenCalledWith(mockOptions[2], 'fontSize');
    });

    it('debería mostrar el valor directamente cuando no coincide con ninguna opción', () => {
        // Valor que no coincide con ningún ID de las opciones
        const nonMatchingValue = '4';

        render(
            <FontSizeSelector
                label="Font Size"
                options={mockOptions}
                value={nonMatchingValue}
                onChangeOption={mockOnChange}
            />
        );

        // Debería mostrar el valor directamente ya que no hay opción con id='4'
        expect(screen.getByText(nonMatchingValue)).toBeInTheDocument();
    });
});
