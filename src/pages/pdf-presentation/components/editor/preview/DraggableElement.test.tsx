// src/pages/pdf-presentation/components/editor/preview/DraggableElement.test.tsx
import { render, screen, fireEvent } from '@testing-library/react';
import { DraggableElement } from './DraggableElement';
import { FIELD } from '@constants/Pdf';
import { FieldType } from '@models/Table';

// Mock del Icon para no depender de la implementación real
jest.mock('@components/icon', () => ({
    Icon: ({ onClick }: any) => (
        <button data-testid="mock-icon" onClick={onClick}>
            mock-trash
        </button>
    ),
}));

// Mock de OBJECTS para controlar lo que renderiza
const mockObjectRenderer = jest.fn(() => <span data-testid="mock-object">RenderedObject</span>);
jest.mock('../pdf', () => ({
    OBJECTS: new Proxy(
        {},
        {
            get: (_, __: string) => mockObjectRenderer,
        }
    ),
}));

const baseElement = {
    id: 'el1',
    type: 'customType',
    x: 50,
    y: 100,
};

describe('DraggableElement', () => {
    const mockOnDragStart = jest.fn();
    const mockOnRemoveItem = jest.fn();

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('renderiza el objeto desde OBJECTS', () => {
        render(
            <DraggableElement
                element={baseElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId={null}
            />
        );

        expect(screen.getByTestId('mock-object')).toBeInTheDocument();
        // Se llama con un solo argumento: { element }
        expect(mockObjectRenderer).toHaveBeenCalledWith({ element: baseElement });
    });

    it('aplica clases base y estilo con posiciones', () => {
        render(
            <DraggableElement
                element={baseElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId={null}
            />
        );

        const [div] = screen.getAllByRole('button'); // el primer "button" es el div con role=button
        expect(div).toHaveStyle({ left: '50px', top: '100px' });
        expect(div).toHaveClass('cursor-pointer absolute border');
        expect(div).toHaveClass('z-10 border-transparent'); // no activo
    });

    it('agrega clase !z-30 cuando el elemento es FIELD o FieldType.Text', () => {
        const fieldElement = { ...baseElement, type: FIELD };
        render(
            <DraggableElement
                element={fieldElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId={null}
            />
        );
        const [div1] = screen.getAllByRole('button');
        expect(div1).toHaveClass('!z-30');

        const textElement = { ...baseElement, type: FieldType.Text };
        render(
            <DraggableElement
                element={textElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId={null}
            />
        );
        const [_, div2] = screen.getAllByRole('button'); // el segundo div
        expect(div2).toHaveClass('!z-30');
    });

    it('aplica clase activa y muestra Icon cuando lastDraggedItemId === element.id', () => {
        render(
            <DraggableElement
                element={baseElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId="el1"
            />
        );

        const [div] = screen.getAllByRole('button'); // primer "button" es el div contenedor
        expect(div).toHaveClass('z-20 border-blue-900');

        const icon = screen.getByTestId('mock-icon');
        expect(icon).toBeInTheDocument();
    });

    it('no muestra el Icon cuando no está activo', () => {
        render(
            <DraggableElement
                element={baseElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId="other"
            />
        );

        expect(screen.queryByTestId('mock-icon')).toBeNull();
    });

    it('llama a onDragStart con el evento y el elemento', () => {
        render(
            <DraggableElement
                element={baseElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId={null}
            />
        );

        const [div] = screen.getAllByRole('button');
        fireEvent.dragStart(div);
        expect(mockOnDragStart).toHaveBeenCalledTimes(1);
        expect(mockOnDragStart.mock.calls[0][1]).toEqual(baseElement);
    });

    it('llama a onRemoveItem cuando se hace click en el Icon', () => {
        render(
            <DraggableElement
                element={baseElement as any}
                onDragStart={mockOnDragStart}
                onRemoveItem={mockOnRemoveItem}
                lastDraggedItemId="el1"
            />
        );

        const icon = screen.getByTestId('mock-icon');
        fireEvent.click(icon);
        expect(mockOnRemoveItem).toHaveBeenCalledTimes(1);
    });
});
