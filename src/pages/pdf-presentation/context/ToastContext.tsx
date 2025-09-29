import { ReactNode } from 'react';
import { IToastContext } from '@models/Toast';
import { ToastContext } from '.';

export const ToastProvider: React.FC<{ children: ReactNode; value: IToastContext }> = ({ children, value }) => {
    return <ToastContext.Provider value={value}>{children}</ToastContext.Provider>;
};
