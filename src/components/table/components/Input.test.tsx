import { render, screen, fireEvent } from '@testing-library/react';
import { Input, Select } from './Input';
import { IFieldProps } from '.';

const DEFAULT_FORM_VALUES = {
    maxLength: 300,
    name: 'test',
    isCustom: false,
    test: 'prueba',
};

describe('Input', () => {
    it('Campo Editable', () => {
        render(<Input handleChange={() => {}} isEditable={true} item={DEFAULT_FORM_VALUES} />);
        const input = screen.getByRole('textbox');
        expect(input).not.toBeDisabled();
        expect(input).toHaveValue('prueba');
    });
    it('Campo no Editable', () => {
        render(<Input handleChange={() => {}} isEditable={false} item={DEFAULT_FORM_VALUES} />);
        const input = screen.getByRole('textbox');
        expect(input).toBeDisabled();
    });

    it('llama al handleChange cuando se escribe en el campo', () => {
        const handleChange = jest.fn();
        render(<Input handleChange={handleChange} isEditable={false} item={DEFAULT_FORM_VALUES} />);
        const input = screen.getByRole('textbox');
        fireEvent.change(input, { target: { value: 'Evento handleChange' } });
        expect(handleChange).toHaveBeenCalledTimes(1);
    });
});

describe('select', () => {
    const options = [
        { label: 'Opción A', value: 'valor 1' },
        { label: 'Opción B', value: 'valor 2' },
    ];

    const baseProps = (): IFieldProps => ({
        handleChange: (): void => {},
        isEditable: false,
        item: {
            options,
            name: 'test',
        },
    });

    it('Validación mapeo de Select', () => {
        render(<Select {...baseProps()} />);
        const input = screen.getByRole('combobox');
        fireEvent.change(input, { target: { value: 'Re' } });
        expect(screen.getByText('Opción A')).toBeInTheDocument();
        expect(screen.getByText('Opción B')).toBeInTheDocument();
    });
});
