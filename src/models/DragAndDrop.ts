import type { DragEvent as ReactDragEvent } from 'react';
import type { IMargins } from '@pages/pdf-presentation/context';
import type { IGenericRecord } from './GenericRecord';

/**
 * Interface for actions related to dropped items management
 *
 * @typeParam clearPageItems: (pageId: string) => void - Removes all dropped items associated with the specified page
 * @typeParam onRemoveItem: (pageId: string, itemId: string) => void - Removes a specific dropped item by its ID from the specified page
 * @typeParam reset: () => void -  Resets all internal states and references to their initial values
 * @typeParam updateDroppedItems: (newDroppedItems: Record<string, IGenericRecord[]>) => void - Updates the dropped items
 */
export interface IDragAndDropActions {
    clearPageItems: (pageId: string) => void;
    onRemoveItem: (pageId: string, itemId: string) => void;
    reset: () => void;
    updateDroppedItems: (newDroppedItems: Record<string, IGenericRecord[]>) => void;
}
/**
 * Interface for handlers related to drag and drop events
 *
 * @typeParam onDragStart: (event: DragEvent, item: IGenericRecord) => void - Initiates dragging of a given element
 * @typeParam onDrop: (event: DragEvent, data: { margins: IMargins; pageId: string }) => void - Handles dropping an element into the preview
 * @typeParam onDragEnd: () => void - Finalizes the current drag and clears temporary state
 */
export interface IDragAndDropHandlers {
    onDragStart: (event: DragEvent, item: IGenericRecord) => void;
    onDrop: (event: DragEvent, data: { margins: IMargins; pageId: string }) => void;
    onDragEnd: () => void;
}

/**
 * Drag and Drop context values and functions
 *
 * @typeParam actions: IDragAndDropActions - Actions for managing dropped items
 * @typeParam droppedItems: Record<string, IGenericRecord[]> - List of elements dropped into the preview with their positions
 * @typeParam handlers: IDragAndDropHandlers - Handlers for drag and drop events
 * @typeParam lastDraggedItemId: string | null - Returns the ID of the last dragged item or null if no item has been dragged
 */
export interface IDragAndDropContext {
    actions: IDragAndDropActions;
    droppedItems: Record<string, IGenericRecord[]>;
    handlers: IDragAndDropHandlers;
    lastDraggedItemId: string | null;
}

/**
 * Drag event type based on React's drag event, defaults to HTMLElement
 *
 * @typeParam T - The element being dragged
 */
export type DragEvent<T = HTMLElement> = ReactDragEvent<T>;
