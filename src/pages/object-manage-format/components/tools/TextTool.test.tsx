import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { TextTool } from './TextTool';
import { ManageObjectContext, IElement } from '@pages/object-manage-format/context';
import type { ContextType } from 'react';
import { ObjectType } from '@constants/ObjectsEditor';

jest.mock('@components/font-size-selector', () => ({
    FontSizeSelector: jest.fn(({ onChangeOption }) => (
        <button data-testid="mock-font-selector" onClick={() => onChangeOption({ value: 16, label: '16px' })}>
            FontSizeSelector
        </button>
    )),
}));
jest.mock('./ColorPicker', () => jest.fn(({ onChange }) => (
    <input
        data-testid="mock-color-picker"
        type="text"
        onChange={e => onChange({ target: { value: e.target.value } })}
    />
)));

const mockUpdateElementStyles = jest.fn();
const mockUpdateElementProperties = jest.fn();

const baseElement: IElement = {
    productId: 'p1', name: 'Text Element', identifier: 'txt1', objectType: ObjectType.Generic, type: 'TEXT',
    content: 'Hello World',
    style: { textAlign: 'left', fontSize: '14px', color: '#000000' },
};

describe('TextTool Component', () => {
    const mockContextValue: ContextType<typeof ManageObjectContext> = {
        element: baseElement,
        updateElementStyles: mockUpdateElementStyles,
        updateElementProperties: mockUpdateElementProperties,
        setElement: jest.fn(),
        handleClickElement: jest.fn(),
        selectedElementType: null,
        setSelectedElementType: jest.fn(),
    };

    const renderTextTool = (element: IElement) => {
        return render(
            <ManageObjectContext.Provider value={{ ...mockContextValue, element }}>
                <TextTool />
            </ManageObjectContext.Provider>
        );
    };

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería llamar a updateElementStyles al cambiar la alineación del texto', async () => {
        const user = userEvent.setup();
        renderTextTool(baseElement);

        const alignContainer = screen.getByText('Alineación').parentElement!;
        const alignButtons = within(alignContainer).getAllByRole('button');
        await user.click(alignButtons[1]);

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('textAlign', 'justify');
    });

    it('debería llamar a updateElementStyles al cambiar el estilo del texto (ej. negrita)', async () => {
        const user = userEvent.setup();
        renderTextTool(baseElement);

        const styleContainer = screen.getByText('Estilos').parentElement!;
        const styleButtons = within(styleContainer).getAllByRole('button');

        await user.click(styleButtons[0]);

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('fontWeight', 'bold');
    });

    describe('Funcionalidad de Listas', () => {
        it('debería convertir texto plano a una lista de viñetas', async () => {
            const user = userEvent.setup();
            renderTextTool({ ...baseElement, content: 'Linea 1\nLinea 2' });

            const listContainer = screen.getByText('Lista').parentElement!;
            const listButtons = within(listContainer).getAllByRole('button');
            const bulletListButton = listButtons[0];

            await user.click(bulletListButton);

            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', '• Linea 1\n• Linea 2');
        });

        it('debería convertir una lista de viñetas a texto plano (toggle)', async () => {
            const user = userEvent.setup();
            const { rerender } = renderTextTool({ ...baseElement, content: 'Texto Original' });

            const listContainer = screen.getByText('Lista').parentElement!;
            const bulletListButton = within(listContainer).getAllByRole('button')[0];

            await user.click(bulletListButton);
            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', '• Texto Original');

            const updatedContextValue: ContextType<typeof ManageObjectContext> = {
                ...mockContextValue,
                element: {...baseElement, content: '• Texto Original'}
            };

            rerender(
                <ManageObjectContext.Provider value={updatedContextValue}>
                    <TextTool />
                </ManageObjectContext.Provider>
            );

            await user.click(bulletListButton);

            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', 'Texto Original');
        });

        it('debería convertir una lista numerada a una lista de viñetas', async () => {
            const user = userEvent.setup();
            renderTextTool({ ...baseElement, content: '1. Linea 1\n2. Linea 2' });

            const listContainer = screen.getByText('Lista').parentElement!;
            const listButtons = within(listContainer).getAllByRole('button');
            const bulletListButton = listButtons[0];

            await user.click(bulletListButton);

            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', '• Linea 1\n• Linea 2');
        });

        it('debería manejar correctamente el contenido vacío', async () => {
            const user = userEvent.setup();
            renderTextTool({ ...baseElement, content: '' });

            const listContainer = screen.getByText('Lista').parentElement!;
            const listButtons = within(listContainer).getAllByRole('button');
            const bulletListButton = listButtons[0];

            await user.click(bulletListButton);

            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', '• ');
        });

        it('debería llamar a updateElementStyles al cambiar el tamaño de la fuente', async () => {
            const user = userEvent.setup();
            renderTextTool(baseElement);
            const fontSizeButton = screen.getByTestId('mock-font-selector');
            await user.click(fontSizeButton);
            expect(mockUpdateElementStyles).toHaveBeenCalledWith('fontSize', 16);
        });

        it('debería llamar a updateElementStyles al cambiar el color del texto', async () => {
            const user = userEvent.setup();
            renderTextTool(baseElement);
            const colorPicker = screen.getByTestId('mock-color-picker');
            await user.type(colorPicker, '#FF0000');
            expect(mockUpdateElementStyles).toHaveBeenCalledWith('color', '#FF0000');
        });
    });
});
