// src/pages/object-manage-format/components/elements/Text.test.tsx
import { render, screen, fireEvent } from '@testing-library/react';
import { Text } from './Text';
import { IElement, ManageObjectContext } from '@pages/object-manage-format/context';
import { ObjectType } from '@constants/ObjectsEditor';

const mockUpdateElementProperties = jest.fn();
const mockSetElement = jest.fn();

const elementMock: IElement = {
    productId: 'prod-123',
    name: 'Sample Text',
    identifier: 'text-001',
    objectType: ObjectType.Generic,
    content: 'Hola Mundo',
    type: 'TEXT',
    style: { width: 100, height: 50, color: 'black' },
};

const renderWithContext = (ui: React.ReactNode) => {
    return render(
        <ManageObjectContext.Provider
            value={
                {
                    updateElementProperties: mockUpdateElementProperties,
                    setElement: mockSetElement,
                } as any
            }
        >
            {ui}
        </ManageObjectContext.Provider>
    );
};

describe('Text Component', () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debe renderizar un <p> en modo PDF', () => {
        renderWithContext(<Text element={elementMock} isPdfMode />);
        const pEl = screen.getByText('Hola Mundo');
        expect(pEl.tagName).toBe('P');
    });

    it('debe renderizar un <textarea> en modo normal', () => {
        renderWithContext(<Text element={elementMock} />);
        const textarea = screen.getByDisplayValue('Hola Mundo');
        expect(textarea.tagName).toBe('TEXTAREA');
    });

    it('debe aplicar clases de preview cuando isPreviewMode es true', () => {
        renderWithContext(<Text element={elementMock} isPreviewMode />);
        const textarea = screen.getByDisplayValue('Hola Mundo');
        expect(textarea).toHaveClass('w-full h-full');
    });

    it('debe llamar a updateElementProperties al cambiar el contenido', () => {
        renderWithContext(<Text element={elementMock} />);
        const textarea = screen.getByDisplayValue('Hola Mundo');
        fireEvent.change(textarea, { target: { value: 'Nuevo texto' } });
        expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', 'Nuevo texto');
    });

    it('debe llamar a setElement al montar', () => {
        renderWithContext(<Text element={elementMock} />);
        expect(mockSetElement).toHaveBeenCalled();
    });
});
