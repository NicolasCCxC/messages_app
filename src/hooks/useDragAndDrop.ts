import { useState, useRef, useCallback, useMemo } from 'react';
import { IDragAndDropContext, DragEvent } from '@models/DragAndDrop';
import type { IGenericRecord } from '@models/GenericRecord';
import { IMargins } from '@pages/pdf-presentation/context';
import { calculateOffset, computeDropPosition, addOrUpdateDroppedItem } from '@utils/DragAndDrop';
import { removeProperties } from '@utils/Object';

export const useDragAndDrop = (): IDragAndDropContext => {
    const [droppedItems, setDroppedItems] = useState<Record<string, IGenericRecord[]>>({});

    const lastDraggedItemId = useRef<string | null>(null);
    const dragItemRef = useRef<IGenericRecord | null>(null);
    const dragOffsetRef = useRef<{ x: number; y: number }>({ x: 0, y: 0 });
    const idCounter = useRef<number>(0);

    const clearPageItems = useCallback((pageId: string): void => {
        setDroppedItems(prev => removeProperties(prev, [pageId]));
    }, []);

    const onDragStart = useCallback((event: DragEvent, item: IGenericRecord): void => {
        calculateOffset({ event, item, dragItemRef, dragOffsetRef });
        lastDraggedItemId.current = dragItemRef.current?.id;
    }, []);

    const onDrop = useCallback((event: DragEvent, data: { margins: IMargins; pageId: string }): void => {
        event.preventDefault();
        if (!dragItemRef.current) return;

        const position = computeDropPosition({
            event,
            margins: data.margins,
            dragItem: dragItemRef.current,
            dragOffset: dragOffsetRef.current,
        });

        setDroppedItems(prev => {
            const currentPageItems = prev[data.pageId] ?? [];
            const updatedPageItems = addOrUpdateDroppedItem({
                currentItems: currentPageItems,
                dragItem: dragItemRef.current!,
                position,
                idCounter,
                lastDraggedItemId,
            });

            return { ...prev, [data.pageId]: updatedPageItems };
        });
    }, []);

    const onDragEnd = useCallback((): void => {
        dragItemRef.current = null;
        dragOffsetRef.current = { x: 0, y: 0 };
    }, []);

    const onRemoveItem = useCallback((pageId: string, itemId: string): void => {
        setDroppedItems(prev => ({
            ...prev,
            [pageId]: prev[pageId]?.filter(item => item.id !== itemId) ?? [],
        }));
    }, []);

    const reset = useCallback((): void => {
        setDroppedItems({});
        dragItemRef.current = null;
        dragOffsetRef.current = { x: 0, y: 0 };
        idCounter.current = 0;
        lastDraggedItemId.current = null;
    }, []);

    const updateDroppedItems = useCallback((newDroppedItems: Record<string, IGenericRecord[]>): void => {
        setDroppedItems(newDroppedItems);
    }, []);

    const actions = useMemo(
        () => ({ clearPageItems, onRemoveItem, reset, updateDroppedItems }),
        [clearPageItems, onRemoveItem, reset, updateDroppedItems]
    );

    const handlers = useMemo(() => ({ onDragStart, onDrop, onDragEnd }), [onDragStart, onDrop, onDragEnd]);

    return {
        actions,
        droppedItems,
        handlers,
        lastDraggedItemId: lastDraggedItemId.current,
    };
};
