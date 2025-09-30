import { ObjectType } from '@constants/ObjectsEditor';
import { IElement, ManageObjectContext } from '@pages/object-manage-format/context';
import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ContextType } from 'react';
import { TextTool } from './TextTool';

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

        it('debería almacenar el texto original en originalTextRef al convertir a lista de viñetas', async () => {
            // Este test verifica que el texto original se guarda correctamente en originalTextRef
            const user = userEvent.setup();
            const originalText = 'Texto para guardar';
            renderTextTool({ ...baseElement, content: originalText });

            const listContainer = screen.getByText('Lista').parentElement!;
            const bulletListButton = within(listContainer).getAllByRole('button')[0];

            await user.click(bulletListButton);

            // Primero se convierte a lista de viñetas
            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', '• Texto para guardar');

            // Ahora simulamos que el componente se ha actualizado con el nuevo contenido
            const updatedContextValue: ContextType<typeof ManageObjectContext> = {
                ...mockContextValue,
                element: {...baseElement, content: '• Texto para guardar'}
            };

            render(
                <ManageObjectContext.Provider value={updatedContextValue}>
                    <TextTool/>
                </ManageObjectContext.Provider>
            );

            // Hacemos clic de nuevo para volver al texto original
            await user.click(bulletListButton);

            // Verificamos que se recupera el texto original
            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', originalText);
        });

        it('debería establecer correctamente activeListType al convertir a lista de viñetas', async () => {
            // Este test verifica que activeListType se establece correctamente
            const user = userEvent.setup();
            const { rerender } = renderTextTool({ ...baseElement, content: 'Texto normal' });

            const listContainer = screen.getByText('Lista').parentElement!;
            const bulletListButton = within(listContainer).getAllByRole('button')[0];

            await user.click(bulletListButton);

            // Verificamos que el contenido se actualiza
            expect(mockUpdateElementProperties).toHaveBeenCalledWith('content', '• Texto normal');

            // Simulamos la actualización del componente con el nuevo contenido
            const updatedContextValue: ContextType<typeof ManageObjectContext> = {
                ...mockContextValue,
                element: {...baseElement, content: '• Texto normal'}
            };

            rerender(
                <ManageObjectContext.Provider value={updatedContextValue}>
                    <TextTool />
                </ManageObjectContext.Provider>
            );

            // Verificamos que el botón de lista de viñetas aparece como activo
            const updatedBulletButton = within(screen.getByText('Lista').parentElement!).getAllByRole('button')[0];
            expect(updatedBulletButton.querySelector('svg')).toHaveAttribute('data-active', 'true');
        });

        it('debería manejar correctamente el caso de texto con formato mixto', async () => {
            // Este test verifica el comportamiento con texto que tiene formato mixto
            const user = userEvent.setup();
            const mixedContent = 'Línea normal\n• Línea con viñeta\n1. Línea numerada';
            renderTextTool({ ...baseElement, content: mixedContent });

            const listContainer = screen.getByText('Lista').parentElement!;
            const bulletListButton = within(listContainer).getAllByRole('button')[0];

            await user.click(bulletListButton);

            // Al no ser una lista de viñetas o numerada válida, debería convertir todo a lista de viñetas
            expect(mockUpdateElementProperties).toHaveBeenCalledWith(
                'content',
                '• Línea normal\n• • Línea con viñeta\n• 1. Línea numerada'
            );
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
