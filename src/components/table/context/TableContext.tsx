import { ReactNode } from 'react';
import { TableContext, ITableContext } from '.';

export const TableProvider: React.FC<{ children: ReactNode; value: ITableContext }> = ({ children, value }) => (
    <TableContext.Provider value={value}>{children}</TableContext.Provider>
);
