import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { Preview } from './Preview';
import { EditorContext, DragAndDropContext, ToastContext } from '@pages/pdf-presentation/context';
import { useAppDispatch } from '@redux/store';
import { createFormat, updateFormat } from '@redux/pdf/actions';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { separateElements } from '.';

jest.mock('@redux/store');

const mockCreateFormatAction = { type: 'pdf/createFormat/mocked' };
const mockUpdateFormatAction = { type: 'pdf/updateFormat/mocked' };
jest.mock('@redux/pdf/actions', () => ({
    createFormat: jest.fn(() => mockCreateFormatAction),
    updateFormat: jest.fn(() => mockUpdateFormatAction),
    getFormats: { fulfilled: 'pdf/getFormats/fulfilled' },
    getProductObjects: { fulfilled: 'pdf/getProductObjects/fulfilled' },
}));

jest.mock('.', () => ({
    ...jest.requireActual('.'),
    separateElements: jest.fn(),
}));

jest.mock('./DraggableElement', () => ({
    DraggableElement: jest.fn(() => <div data-testid="mock-draggable-element" />),
}));
jest.mock('@components/button', () => ({
    Button: jest.fn(({ onClick, text }) => <button onClick={onClick}>{text}</button>),
}));
jest.mock('@components/icon', () => ({
    Icon: jest.fn(({ onClick }) => <button data-testid="mock-trash-icon" onClick={onClick} />),
}));

const mockedUseAppDispatch = useAppDispatch as unknown as jest.Mock;
const mockedSeparateElements = separateElements as jest.Mock;
const mockDispatch = jest.fn();

const mockEditorContext = {
    formatConfig: { id: 'format-1', isNew: true, margins: { top: 0, bottom: 0, left: 0, right: 0 }, pageSize: { value: 'Letter', label: 'Letter' }, productId: 'prod-1', font: { value: 'Arial', label: 'Arial' }, version: '1' },
    pages: ['page-1'],
    reset: jest.fn(),
    updatePages: jest.fn(),
    updateFormatConfig: jest.fn(),
};
const mockDragAndDropContext = {
    actions: { clearPageItems: jest.fn(), onRemoveItem: jest.fn(), reset: jest.fn(), updateDroppedItems: jest.fn() },
    handlers: { onDragStart: jest.fn(), onDrop: jest.fn(), onDragEnd: jest.fn() },
    droppedItems: { 'page-1': [{ id: 'item-1' }, { id: 'item-2' }] },
    lastDraggedItemId: null,
};
const mockToastContext = {
    toggleToast: jest.fn(),
    toast: null,
};

const renderPreview = (
    editorCtx = mockEditorContext,
    dragCtx = mockDragAndDropContext,
    toastCtx = mockToastContext
) => {
    mockedUseAppDispatch.mockReturnValue(mockDispatch);
    return render(
        <ToastContext.Provider value={toastCtx}>
            <DragAndDropContext.Provider value={dragCtx}>
                <EditorContext.Provider value={editorCtx}>
                    <Preview toggleEditor={jest.fn()} />
                </EditorContext.Provider>
            </DragAndDropContext.Provider>
        </ToastContext.Provider>
    );
};

describe('Preview Component', () => {

    beforeEach(() => {
        jest.clearAllMocks();
        mockDispatch.mockResolvedValue({ payload: { message: 'Success!' } });
    });

    it('debería renderizar las páginas y los elementos de los contextos', () => {
        const { container } = renderPreview();
        const pageElement = container.querySelector('.preview__page');
        expect(pageElement).toBeInTheDocument();
        expect(screen.getAllByTestId('mock-draggable-element')).toHaveLength(2);
    });

    it('debería llamar a updatePages al hacer clic en "Agregar página"', async () => {
        const user = userEvent.setup();
        renderPreview();
        const addPageButton = screen.getByRole('button', { name: '+Agregar página' });
        await user.click(addPageButton);
        expect(mockEditorContext.updatePages).toHaveBeenCalled();
    });

    it('debería llamar a updatePages y clearPageItems al eliminar una página', async () => {
        const user = userEvent.setup();
        const pages = ['page-1', 'page-2'];
        renderPreview({ ...mockEditorContext, pages });
        const deleteButton = screen.getByTestId('mock-trash-icon');
        await user.click(deleteButton);
        expect(mockEditorContext.updatePages).toHaveBeenCalled();
        expect(mockDragAndDropContext.actions.clearPageItems).toHaveBeenCalledWith('page-2');
    });

    describe('Submisión de Formato', () => {
        it('debería mostrar un toast de error si la validación falla', async () => {
            const user = userEvent.setup();
            mockedSeparateElements.mockReturnValue({ elements: [], fields: [] });
            
            renderPreview();
            
            const submitButton = screen.getByRole('button', { name: 'Crear formato' });
            await user.click(submitButton);

            expect(mockToastContext.toggleToast).toHaveBeenCalledWith(REQUIRED_FIELDS);
            expect(mockDispatch).not.toHaveBeenCalled();
        });

        it('debería despachar la acción "createFormat" si es un formato nuevo', async () => {
            const user = userEvent.setup();
            mockedSeparateElements.mockReturnValue({ elements: [{id: 'el-1'}], fields: [{id: 'f-1'}] });

            renderPreview();
            
            const submitButton = screen.getByRole('button', { name: 'Crear formato' });
            await user.click(submitButton);

            expect(createFormat).toHaveBeenCalledWith(expect.any(Object));
            expect(mockDispatch).toHaveBeenCalledWith(mockCreateFormatAction);

            await waitFor(() => {
                expect(mockToastContext.toggleToast).toHaveBeenCalledWith('Success!', undefined);
            });
            expect(mockEditorContext.reset).toHaveBeenCalled();
            expect(mockDragAndDropContext.actions.reset).toHaveBeenCalled();
        });

        it('debería despachar la acción "updateFormat" si se está editando un formato', async () => {
            const user = userEvent.setup();
            mockedSeparateElements.mockReturnValue({ elements: [{id: 'el-1'}], fields: [{id: 'f-1'}] });

            const editingContext = { ...mockEditorContext, formatConfig: { ...mockEditorContext.formatConfig, isNew: false } };
            renderPreview(editingContext);

            const submitButton = screen.getByRole('button', { name: 'Crear formato' });
            await user.click(submitButton);

            expect(updateFormat).toHaveBeenCalledWith(expect.any(Object));
            expect(mockDispatch).toHaveBeenCalledWith(mockUpdateFormatAction);

            await waitFor(() => {
                expect(mockToastContext.toggleToast).toHaveBeenCalledWith('Success!', undefined);
            });
        });
    });
});