import { NotificationType } from '@components/toast';

/**
 * Toast message structure used for UI notifications
 *
 * @typeParam message: string - The text to be displayed in the toast
 * @typeParam type: NotificationType - Optional toast type (success, error, etc.)
 */
export interface IToast {
    message: string;
    type?: NotificationType;
}

/**
 * Return type of the useToast hook
 *
 * @typeParam toast: IToast | null - Current toast message or null if not visible
 * @typeParam toggleToast: ToggleToast - Function to show or hide the toast
 */
export interface IToastContext {
    toast: IToast | null;
    toggleToast: ToggleToast;
}

/**
 * Function to toggle the toast by showing a message or hiding it
 */
export type ToggleToast = (message: string | null, type?: NotificationType) => void;
