import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { SidebarOption } from './SidebarOption';
import { ManageObjectContext, IElement } from '@pages/object-manage-format/context';
import type { ContextType } from 'react';
import { ObjectType } from '@constants/ObjectsEditor';

// --- CORRECCIÓN AQUÍ ---
// Hacemos un "mock parcial": mantenemos las exportaciones originales y solo sobreescribimos DialogModal.
jest.mock('@components/modal', () => {
    const originalModule = jest.requireActual('@components/modal');
    return {
        __esModule: true, // Necesario para la interoperabilidad de módulos
        ...originalModule, // Mantenemos todas las exportaciones originales (como DialogModalType)
        // Y solo sobreescribimos el componente DialogModal con nuestro mock
        DialogModal: jest.fn(({ onConfirm, onClose }) => (
            <div data-testid="mock-modal">
                <button onClick={onConfirm}>Confirm</button>
                <button onClick={onClose}>Close</button>
            </div>
        )),
    };
});
jest.mock('@components/icon', () => ({
    Icon: jest.fn(() => <div data-testid="mock-icon" />),
}));

// -- Configuración del Test (sin cambios) --
const mockHandleClickElement = jest.fn();

const cleanElement: IElement = {
    productId: '', name: '', identifier: '', objectType: ObjectType.Generic, type: 'TEXT',
    header: { columns: [{ id: 'h1' }] },
    body: { cells: [] },
    style: { color: 'red', fontSize: '12px' },
};

const mockContextValue: ContextType<typeof ManageObjectContext> = {
    selectedElementType: null,
    handleClickElement: mockHandleClickElement,
    element: cleanElement,
    setElement: jest.fn(),
    updateElementProperties: jest.fn(),
    updateElementStyles: jest.fn(),
    setSelectedElementType: jest.fn(),
};

const renderSidebarOption = (contextProps: Partial<ContextType<typeof ManageObjectContext>>) => {
    return render(
        <ManageObjectContext.Provider value={{ ...mockContextValue, ...contextProps }}>
            <SidebarOption icon="text" label="Texto" />
        </ManageObjectContext.Provider>
    );
};


describe('SidebarOption Component', () => {

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería llamar a handleClickElement directamente si el "element" está limpio', async () => {
        const user = userEvent.setup();
        renderSidebarOption({ element: cleanElement });

        const optionButton = screen.getByRole('button');
        await user.click(optionButton);

        expect(screen.queryByTestId('mock-modal')).not.toBeInTheDocument();
        expect(mockHandleClickElement).toHaveBeenCalledWith('TEXT');
    });

    it('debería mostrar el modal si el "element" tiene contenido', async () => {
        const user = userEvent.setup();
        const dirtyElement: IElement = { ...cleanElement, content: 'hay contenido' };
        renderSidebarOption({ element: dirtyElement });

        const optionButton = screen.getByRole('button');
        await user.click(optionButton);

        expect(screen.getByTestId('mock-modal')).toBeInTheDocument();
        expect(mockHandleClickElement).not.toHaveBeenCalled();
    });
    
    test.each([
        { condition: 'content', element: { ...cleanElement, content: 'hola' } },
        { condition: 'image', element: { ...cleanElement, image: 'url' } },
        { condition: 'body cells', element: { ...cleanElement, body: { cells: [{ id: 'c1' }] } } },
        { condition: 'header columns > 1', element: { ...cleanElement, header: { columns: [{ id: 'h1' }, { id: 'h2' }] } } },
        { condition: 'style properties > 2', element: { ...cleanElement, style: { color: 'red', fontSize: '12px', width: 100 } } },
    ])('debería mostrar el modal si la condición "$condition" se cumple', async ({ element }) => {
        const user = userEvent.setup();
        renderSidebarOption({ element });
        await user.click(screen.getByRole('button'));
        expect(screen.getByTestId('mock-modal')).toBeInTheDocument();
    });

    it('debería llamar a handleClickElement al confirmar el modal', async () => {
        const user = userEvent.setup();
        const dirtyElement: IElement = { ...cleanElement, content: 'hay contenido' };
        renderSidebarOption({ element: dirtyElement });

        await user.click(screen.getByRole('button'));
        expect(screen.getByTestId('mock-modal')).toBeInTheDocument();
        
        const confirmButton = screen.getByText('Confirm');
        await user.click(confirmButton);

        expect(mockHandleClickElement).toHaveBeenCalledWith('TEXT');
        expect(screen.queryByTestId('mock-modal')).not.toBeInTheDocument();
    });

    it('debería cerrar el modal al cancelarlo', async () => {
        const user = userEvent.setup();
        const dirtyElement: IElement = { ...cleanElement, content: 'hay contenido' };
        renderSidebarOption({ element: dirtyElement });

        await user.click(screen.getByRole('button'));
        expect(screen.getByTestId('mock-modal')).toBeInTheDocument();
        
        const closeButton = screen.getByText('Close');
        await user.click(closeButton);

        expect(mockHandleClickElement).not.toHaveBeenCalled();
        expect(screen.queryByTestId('mock-modal')).not.toBeInTheDocument();
    });

    it('debería aplicar estilos de "seleccionado" si el tipo de elemento del contexto coincide', () => {
        renderSidebarOption({ selectedElementType: 'TEXT' as any });
        
        const optionButton = screen.getByRole('button');
        expect(optionButton).toHaveClass('bg-blue-dark');
        expect(screen.getByText('Texto')).toHaveClass('text-white');
    });

    it('debería aplicar estilos normales si el tipo de elemento no coincide', () => {
        renderSidebarOption({ selectedElementType: 'IMAGE' as any });

        const optionButton = screen.getByRole('button');
        expect(optionButton).toHaveClass('bg-white');
        expect(screen.getByText('Texto')).toHaveClass('text-black');
    });
});