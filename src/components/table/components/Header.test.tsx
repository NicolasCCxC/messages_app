import { render, screen } from '@testing-library/react';
import { Header } from './Header';
import { IHeaderField } from '@models/Table';

jest.mock('@components/icon', () => ({
    Icon: jest.fn(({ name, className }) => <span data-testid={`icon-${name}`} className={className} />),
}));

describe('Header Component', () => {
    const fields: IHeaderField[] = [
        { value: 'Nombre', className: 'custom-class', icon: 'cancelWhite' },
        { value: 'Edad', className: '' },
        { value: '', className: 'hidden' }, // este no debe renderizarse
    ];

    it('debe renderizar solo los th con value', () => {
        render(
            <table>
                <Header fields={fields} />
            </table>
        );
        const thElements = screen.getAllByRole('columnheader');
        expect(thElements).toHaveLength(2); // solo 'Nombre' y 'Edad'
        expect(thElements[0]).toHaveTextContent('Nombre');
        expect(thElements[1]).toHaveTextContent('Edad');
    });

    it('debe renderizar Icon cuando se pasa icon', () => {
        render(
            <table>
                <Header fields={fields} />
            </table>
        );
        const icon = screen.getByTestId('icon-cancelWhite');
        expect(icon).toBeInTheDocument();
        expect(icon).toHaveClass('inline mr-2.5');
    });

    it('debe aplicar className personalizado', () => {
        render(
            <table>
                <Header fields={fields} />
            </table>
        );
        const nombreTh = screen.getByText('Nombre').closest('th');
        expect(nombreTh).toHaveClass('custom-class');
    });

    it('cada th debe tener clases por defecto', () => {
        render(
            <table>
                <Header fields={fields} />
            </table>
        );
        const edadTh = screen.getByText('Edad').closest('th');
        expect(edadTh).toHaveClass('border border-b-0 border-gray text-white bg-blue-dark text-left px-2.5 py-[0.1563rem]');
    });
});
