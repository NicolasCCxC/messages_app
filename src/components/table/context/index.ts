import { createContext } from 'react';
import { IData, IEditing } from '@models/Table';

export { TableProvider } from './TableContext';

/**
 * This describes the properties of the context
 *
 * @typeParam data: IData - This groups everything related to the data in the table
 * @typeParam onDeleteRow: (id: string) => void - This is used to delete each row from the table
 * @typeParam onUpdateRow: () => void - This is used to save changes
 */
export interface ITableContext {
    data: IData;
    editing: Omit<IEditing, 'onFieldChange'>;
}

/**
 * Table context
 */
export const TableContext = createContext<ITableContext>({
    data: {
        all: [],
        current: [],
        pages: 1,
        update: () => {},
    },
    editing: {
        onDeleteRow: () => {},
        onUpdateRow: () => {},
        onPageChange: () => {},
    },
});
