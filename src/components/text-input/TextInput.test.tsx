import { render, screen, fireEvent } from '@testing-library/react';
import { ITextInputProps } from '.';
import { TextInput } from './TextInput';

const baseProps = (): ITextInputProps => ({
    value: '',
    onChange: (): void => {},
    disabled: false,
    name: 'Nombre',
    placeholder: 'Escribe tu nombre',
});

describe('TextInput', () => {
    it('Muestra correctamente label y placeholder', () => {
        render(<TextInput {...baseProps()} />);
        expect(screen.queryByText('Escribe tu nombre')).toBeInTheDocument();
    });
    it('Campo Editable', () => {
        render(<TextInput {...baseProps()} value="Carlos" />);
        const input = screen.getByRole('textbox');
        expect(input).not.toBeDisabled();
        expect(input).toHaveValue('Carlos');
    });
    it('Campo no Editable', () => {
        render(<TextInput {...baseProps()} disabled={true} />);
        const input = screen.getByRole('textbox');
        expect(input).toBeDisabled();
    });

    it('llama al handleChange cuando se escribe en el campo', () => {
        const onChange = jest.fn();
        render(<TextInput {...baseProps()} onChange={onChange} />);
        const input = screen.getByRole('textbox');
        fireEvent.change(input, { target: { value: 'Evento handleChange' } });
        expect(onChange).toHaveBeenCalledTimes(1);
    });
});
