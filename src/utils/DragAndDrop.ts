import type { DragEvent } from '@models/DragAndDrop';
import type { IGenericRecord } from '@models/GenericRecord';
import type { IMargins } from '@pages/pdf-presentation/context';

/**
 * Reference to a draggable element
 */
type DraggableRef = React.MutableRefObject<IGenericRecord | null>;

/**
 * This describes the arguments required to calculate the drag offset
 *
 * @typeParam event: DragEvent - The drag event that triggered the calculation
 * @typeParam item: IGenericRecord - The item being dragged
 * @typeParam dragItemRef: DraggableRef - Reference to the currently dragged item
 * @typeParam dragOffsetRef: DraggableRef - Reference to the current drag offset (x and y)
 */
export interface CalculateOffsetArgs {
    event: DragEvent;
    item: IGenericRecord;
    dragItemRef: DraggableRef;
    dragOffsetRef: DraggableRef;
}

/**
 * Calculates the drag offset based on the current event and item position.
 *
 * @param event: DragEvent - Current drag event
 * @param item: IGenericRecord - Item being dragged
 * @param dragItemRef: React.MutableRefObject<IGenericRecord | null> - Reference to the drag item
 * @param dragOffsetRef: React.MutableRefObject<{ x: number; y: number }> - Reference to the drag offset
 */
export const calculateOffset = ({ event, item, dragItemRef, dragOffsetRef }: CalculateOffsetArgs): void => {
    const { left, top, width, height } = (event.currentTarget as HTMLElement).getBoundingClientRect();
    dragOffsetRef.current = { x: event.clientX - left, y: event.clientY - top };
    dragItemRef.current = { ...item, width, height };
};

/**
 * Computes the drop position within the container, considering margins and boundaries.
 *
 * @param params: { event: DragEvent; margins: IMargins; dragItem: IGenericRecord; dragOffset: { x: number; y: number } } - Parameters for calculating drop position
 * @returns { x: number; y: number }
 */
export const computeDropPosition = (params: {
    event: DragEvent;
    margins: IMargins;
    dragItem: IGenericRecord;
    dragOffset: { x: number; y: number };
}): { x: number; y: number } => {
    const { event, margins, dragItem, dragOffset } = params;
    const container = event.currentTarget as HTMLElement;
    const containerRect = container.getBoundingClientRect();

    const itemWidth = dragItem.width ?? 0;
    const itemHeight = dragItem.height ?? 0;

    const rawX = event.clientX - containerRect.left - dragOffset.x;
    const rawY = event.clientY - containerRect.top - dragOffset.y;

    const maxX = containerRect.width - margins.right - itemWidth;
    const maxY = containerRect.height - margins.bottom - itemHeight;

    const x = Math.max(margins.left, Math.min(rawX, maxX));
    const y = Math.max(margins.top, Math.min(rawY, maxY));

    return { x, y };
};

/**
 * Adds or updates a dropped item in the list of current items.
 *
 * @param params: { currentItems: IGenericRecord[]; dragItem: IGenericRecord; position: { x: number; y: number }; idCounter: React.MutableRefObject<number>; lastDraggedItemId: React.MutableRefObject<string | null> } - Parameters for adding or updating an item
 * @returns IGenericRecord[]
 */
export const addOrUpdateDroppedItem = (params: {
    currentItems: IGenericRecord[];
    dragItem: IGenericRecord;
    position: { x: number; y: number };
    idCounter: React.MutableRefObject<number>;
    lastDraggedItemId: React.MutableRefObject<string | null>;
}): IGenericRecord[] => {
    const { currentItems, dragItem, position, idCounter, lastDraggedItemId } = params;
    const exists = currentItems.some(item => item.id === dragItem.id);

    if (exists) {
        return currentItems.map(item => (item.id === dragItem.id ? { ...item, x: position.x, y: position.y } : item));
    }

    idCounter.current += 1;
    lastDraggedItemId.current = `${dragItem.id}-${idCounter.current}`;

    return [
        ...currentItems,
        {
            ...dragItem,
            id: `${dragItem.id}-${idCounter.current}`,
            sourceId: dragItem.id,
            x: position.x,
            y: position.y,
        },
    ];
};
