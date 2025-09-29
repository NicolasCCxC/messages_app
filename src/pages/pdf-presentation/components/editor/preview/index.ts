import { FIELD } from '@constants/Pdf';
import type { DragEvent } from '@models/DragAndDrop';
import type { IGenericRecord } from '@models/GenericRecord';
import type { IMargins } from '@pages/pdf-presentation/context';

export { Preview } from './Preview';

/**
 * Categorized structure of dropped items
 *
 * @typeParam elements: IGenericRecord[] - Array of general elements dropped on the canvas
 * @typeParam fields: IGenericRecord[] - Array of fields dropped on the canvas
 */
export interface ICategorizedItems {
    elements: IGenericRecord[];
    fields: IGenericRecord[];
}

/**
 * Props used by draggable components within the PDF editor
 *
 * @typeParam element: IGenericRecord - The draggable element
 * @typeParam onDragStart: (e: DragEvent, item: IGenericRecord) => void - Handler triggered when drag starts
 * @typeParam onRemoveItem: () => void - Removes a specific dropped item by its ID from the specified page
 * @typeParam lastDraggedItemId: string | null - Returns the ID of the last dragged item or null if no item has been dragged
 */
export interface IDraggableElementProps {
    element: IGenericRecord;
    onDragStart: (e: DragEvent, item: IGenericRecord) => void;
    onRemoveItem: () => void;
    lastDraggedItemId: string | null;
}

/**
 * This builds the payload structure for each page from dropped items
 *
 * @param droppedItems: IGenericRecord - Object containing the dropped items grouped by page
 * @returns IGenericRecord[] - Array of formatted page objects
 */
export const buildPagesPayload = (droppedItems: IGenericRecord): IGenericRecord[] => {
    const pages: IGenericRecord[] = [];
    Object.values(droppedItems).forEach((items, index) => {
        const { elements, fields } = separateElements(items);
        pages.push({
            pageNumber: index + 1,
            elements: formatItemProperties(elements),
            fields: formatItemProperties(fields, true),
        });
    });
    return pages;
};

/**
 * This formats the coordinates and adds an identifier key to each item
 *
 * @param items: IGenericRecord[] - Items to be formatted
 * @param isFieldArray: boolean - Whether the items belong to the fields category
 * @returns IGenericRecord[]
 */
const formatItemProperties = (items: IGenericRecord[], isFieldArray = false): IGenericRecord[] => {
    return items.map(item => {
        const idKey = isFieldArray ? 'fieldId' : 'objectId';
        return { positionX: item.x, positionY: item.y, [idKey]: item.sourceId };
    });
};

/**
 * This separates the given items into elements and fields
 *
 * @param items: IGenericRecord[] - Array of items to be categorized
 * @returns ICategorizedItems
 */
export const separateElements = (items: IGenericRecord[]): ICategorizedItems => {
    const categorizedItems: ICategorizedItems = { elements: [], fields: [] };
    for (const item of items) categorizedItems[item.type === FIELD ? 'fields' : 'elements'].push(item);
    return categorizedItems;
};

/**
 * Converts margin values from strings to numbers
 *
 * @param margins: IMargins - Object containing margin values as strings.
 * @returns IMargins
 */
export const parseMarginsToNumber = (margins: IMargins): IMargins => ({
    top: Number(margins.top),
    bottom: Number(margins.bottom),
    left: Number(margins.left),
    right: Number(margins.right),
});

/**
 * Index value representing the first page in the pages list
 */
export const FIRST_PAGE_INDEX = 0;
