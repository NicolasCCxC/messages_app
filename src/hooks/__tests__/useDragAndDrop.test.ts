import { renderHook, act } from '@testing-library/react';
import { useDragAndDrop } from '../useDragAndDrop';
import * as DragAndDropUtils from '@utils/DragAndDrop';
import * as ObjectUtils from '@utils/Object';

jest.mock('@utils/DragAndDrop');
jest.mock('@utils/Object');

const mockedDragUtils = DragAndDropUtils as jest.Mocked<typeof DragAndDropUtils>;
const mockedObjectUtils = ObjectUtils as jest.Mocked<typeof ObjectUtils>;

describe('useDragAndDrop hook', () => {

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería tener un estado inicial vacío', () => {
        const { result } = renderHook(() => useDragAndDrop());
        expect(result.current.droppedItems).toEqual({});
        expect(result.current.lastDraggedItemId).toBeNull();
    });

    it('debería llamar a calculateOffset en onDragStart', () => {
        const { result } = renderHook(() => useDragAndDrop());
        const mockItem = { id: 'item-1', name: 'Draggable' };
        const mockEvent = {} as any;

        act(() => {
            result.current.handlers.onDragStart(mockEvent, mockItem);
        });

        expect(mockedDragUtils.calculateOffset).toHaveBeenCalledTimes(1);
    });

    it('debería actualizar droppedItems en onDrop', () => {
        const { result } = renderHook(() => useDragAndDrop());
        const mockItem = { id: 'item-1', name: 'Draggable' };
        const mockEvent = { preventDefault: jest.fn() } as any;
        const mockDropData = { 
            margins: { top: 0, left: 0, bottom: 0, right: 0 }, 
            pageId: 'page-1' 
        };
        const updatedItems = [{ ...mockItem, x: 100, y: 100 }];

        mockedDragUtils.calculateOffset.mockImplementation(({ dragItemRef, item }) => {
            if (dragItemRef) {
                dragItemRef.current = item;
            }
        });

        mockedDragUtils.computeDropPosition.mockReturnValue({ x: 100, y: 100 });
        mockedDragUtils.addOrUpdateDroppedItem.mockReturnValue(updatedItems);

        act(() => {
            result.current.handlers.onDragStart(mockEvent, mockItem);
        });
        
        act(() => {
            result.current.handlers.onDrop(mockEvent, mockDropData);
        });

        expect(mockEvent.preventDefault).toHaveBeenCalled();
        expect(mockedDragUtils.computeDropPosition).toHaveBeenCalled();
        expect(mockedDragUtils.addOrUpdateDroppedItem).toHaveBeenCalled();
        expect(result.current.droppedItems['page-1']).toEqual(updatedItems);
    });

    it('no debería hacer nada en onDrop si no hay un item siendo arrastrado', () => {
        const { result } = renderHook(() => useDragAndDrop());
        const mockEvent = { preventDefault: jest.fn() } as any;
        const mockDropData = { 
            margins: { top: 0, left: 0, bottom: 0, right: 0 }, 
            pageId: 'page-1' 
        };

        act(() => {
            result.current.handlers.onDrop(mockEvent, mockDropData);
        });

        expect(mockedDragUtils.computeDropPosition).not.toHaveBeenCalled();
        expect(result.current.droppedItems['page-1']).toBeUndefined();
    });

    it('debería limpiar las referencias en onDragEnd', () => {
        const { result } = renderHook(() => useDragAndDrop());
        const mockItem = { id: 'item-1', name: 'Draggable' };
        const mockEvent = { preventDefault: jest.fn() } as any;

        act(() => {
            result.current.handlers.onDragStart({} as any, mockItem);
        });

        act(() => {
            result.current.handlers.onDragEnd();
        });

        act(() => {
            result.current.handlers.onDrop(mockEvent, { margins: { top: 0, left: 0, bottom: 0, right: 0 }, pageId: 'page-1' });
        });

        expect(mockedDragUtils.computeDropPosition).not.toHaveBeenCalled();
    });

    it('debería remover un item con onRemoveItem', () => {
        const { result } = renderHook(() => useDragAndDrop());
        const initialItems = { 'page-1': [{ id: 'item-1' }, { id: 'item-2' }] };
        
        act(() => {
            result.current.actions.updateDroppedItems(initialItems);
        });
        
        act(() => {
            result.current.actions.onRemoveItem('page-1', 'item-1');
        });

        expect(result.current.droppedItems['page-1']).toHaveLength(1);
        expect(result.current.droppedItems['page-1'][0].id).toBe('item-2');
    });

    it('debería limpiar todos los items de una página con clearPageItems', () => {
        const { result } = renderHook(() => useDragAndDrop());
        const initialItems = { 'page-1': [{ id: 'item-1' }], 'page-2': [{ id: 'item-2' }] };

        mockedObjectUtils.removeProperties.mockReturnValue({ 'page-2': [{ id: 'item-2' }] });
        
        act(() => {
            result.current.actions.updateDroppedItems(initialItems);
        });

        act(() => {
            result.current.actions.clearPageItems('page-1');
        });

        expect(mockedObjectUtils.removeProperties).toHaveBeenCalledWith(initialItems, ['page-1']);
        expect(result.current.droppedItems).toEqual({ 'page-2': [{ id: 'item-2' }] });
    });

    it('debería resetear todo el estado con reset', () => {
        const { result } = renderHook(() => useDragAndDrop());
        
        act(() => {
            result.current.actions.updateDroppedItems({ 'page-1': [{ id: 'item-1' }] });
        });

        act(() => {
            result.current.actions.reset();
        });

        expect(result.current.droppedItems).toEqual({});
    });
});