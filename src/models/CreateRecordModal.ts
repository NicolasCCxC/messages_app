import type { IGenericRecord } from './GenericRecord';

/**
 * This describes the props of the modal
 *
 * @typeParam products: IOption[] - Product list
 * @typeParam toggleModal: () => void - This is used to toggle the modal
 * @typeParam updateNotification: (notification: string) => void - This is used to edit the notification message that is displayed on the screen
 */
export interface ICreateRecordModalProps {
    products: IGenericRecord[];
    toggleModal: () => void;
    updateNotification: (notification: string) => void;
}
