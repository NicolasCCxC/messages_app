import React, { ReactNode } from 'react';
import { useDragAndDrop } from '@hooks/useDragAndDrop';
import { DragAndDropContext } from '.';

export const DragAndDropProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const dragAndDrop = useDragAndDrop();
    return <DragAndDropContext.Provider value={dragAndDrop}>{children}</DragAndDropContext.Provider>;
};
