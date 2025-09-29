export { Toast } from './Toast';

/**
 * These are the different types of notification
 */
export enum NotificationType {
    Error = 'ERROR',
}

/**
 * Interface for the Toast component props
 *
 * @typeParam open: boolean - Determines whether the toast is currently visible or hidden
 * @typeParam message: string - Optional text content to be displayed in the toast notification
 * @typeParam onClose: () => void - Function to be called when the toast needs to be closed
 * @typeParam autoHideDuration: number - Optional duration (in milliseconds) after which the toast should automatically close
 * @typeParam type: NotificationType - Optional notification type
 */
export interface IToastProps {
    open: boolean;
    message?: string;
    onClose: () => void;
    autoHideDuration?: number;
    type?: NotificationType;
}
