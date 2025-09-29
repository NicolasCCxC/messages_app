import { useState } from 'react';
import { NotificationType } from '@components/toast';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { IToast, IToastContext } from '@models/Toast';

/**
 * Custom hook to manage toast visibility and message handling
 *
 * @returns IToastContext - Toast state and toggle function
 */
export const useToast = (): IToastContext => {
    const [toast, setToast] = useState<IToast | null>(null);

    const toggleToast = (message: string | null, type?: NotificationType): void => {
        const newValue = message ? { message, type, ...(message === REQUIRED_FIELDS && { type: NotificationType.Error }) } : null;
        setToast(prev => (prev ? null : newValue));
    };

    return { toast, toggleToast };
};
