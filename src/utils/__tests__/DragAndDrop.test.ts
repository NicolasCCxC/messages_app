/** @jest-environment jsdom */

import { calculateOffset, computeDropPosition, addOrUpdateDroppedItem } from '../DragAndDrop';

const createMockRef = (initialValue: any = null) => ({
    current: initialValue,
});
const createMockRect = (top: number, left: number, width: number, height: number): DOMRect => ({
    top,
    left,
    width,
    height,
    x: left,
    y: top,
    right: left + width,
    bottom: top + height,
    toJSON: () => JSON.stringify(this),
});

const createMockEvent = (clientX: number, clientY: number, containerRect: DOMRect) => ({
    clientX,
    clientY,
    currentTarget: {
        getBoundingClientRect: jest.fn().mockReturnValue(containerRect),
    },
});



describe('DragAndDrop Utilities', () => {

    describe('calculateOffset', () => {
        it('should correctly calculate and update the drag offset and drag item refs', () => {
            const dragItemRef = createMockRef();
            const dragOffsetRef = createMockRef();
            const mockItem = { id: 'item-1', name: 'Draggable Item' };

            const mockElementRect = createMockRect(200, 100, 50, 30);
            
            const mockEvent = {
                clientX: 120,
                clientY: 215,
                currentTarget: {
                    getBoundingClientRect: jest.fn().mockReturnValue(mockElementRect),
                },
            };

            calculateOffset({
                event: mockEvent as any,
                item: mockItem,
                dragItemRef,
                dragOffsetRef,
            });

            expect(dragOffsetRef.current).toEqual({ x: 20, y: 15 });
            expect(dragItemRef.current).toEqual({ ...mockItem, width: 50, height: 30 });
        });
    });

    describe('computeDropPosition', () => {

        const mockContainerRect = createMockRect(50, 50, 500, 400);
        const mockMargins = { top: 10, right: 20, bottom: 30, left: 40 };
        const dragItem = { id: 'item-1', width: 100, height: 80 };
        const dragOffset = { x: 15, y: 25 };

        it('should compute the correct position when dropped inside boundaries', () => {
            const mockEvent = createMockEvent(250, 250, mockContainerRect);
            const position = computeDropPosition({ event: mockEvent as any, margins: mockMargins, dragItem, dragOffset });
            expect(position).toEqual({ x: 185, y: 175 });
        });

        it('should clamp the position to the top and left boundaries if dropped too far', () => {
            const mockEvent = createMockEvent(100, 100, mockContainerRect);
            const position = computeDropPosition({ event: mockEvent as any, margins: mockMargins, dragItem, dragOffset });
            expect(position).toEqual({ x: 40, y: 25 });
        });

        it('should clamp the position to the right and bottom boundaries if dropped too far', () => {
            const mockEvent = createMockEvent(600, 500, mockContainerRect);
            const position = computeDropPosition({ event: mockEvent as any, margins: mockMargins, dragItem, dragOffset });
            expect(position).toEqual({ x: 380, y: 290 });
        });
        

    });

    describe('addOrUpdateDroppedItem', () => {
        const initialItems = [{ id: 'existing-1', x: 10, y: 10, name: 'First Updated' }];
        const idCounter = createMockRef(1);
        const lastDraggedItemId = createMockRef();

        it('should update an existing item if its ID is already in the list', () => {
            const dragItem = { id: 'existing-1', name: 'First Updated' };
            const newPosition = { x: 100, y: 120 };

            const updatedItems = addOrUpdateDroppedItem({
                currentItems: initialItems,
                dragItem,
                position: newPosition,
                idCounter,
                lastDraggedItemId,
            });

            expect(updatedItems).toHaveLength(1);
            expect(updatedItems[0]).toEqual({ ...dragItem, x: 100, y: 120 });
            expect(idCounter.current).toBe(1);
            expect(lastDraggedItemId.current).toBeNull();
        });

        it('should add a new item with a unique ID if it does not exist in the list', () => {
            const dragItem = { id: 'new-item', name: 'Second' };
            const newPosition = { x: 50, y: 60 };

            const updatedItems = addOrUpdateDroppedItem({
                currentItems: initialItems,
                dragItem,
                position: newPosition,
                idCounter,
                lastDraggedItemId,
            });
            
            expect(updatedItems).toHaveLength(2);
            const expectedNewId = `${dragItem.id}-${idCounter.current}`;
            const newItem = updatedItems.find(item => item.sourceId === dragItem.id);

            expect(newItem).toBeDefined();
            expect(newItem?.id).toBe(expectedNewId);
            expect(idCounter.current).toBe(2);
            expect(lastDraggedItemId.current).toBe(expectedNewId);
        });
    });
});