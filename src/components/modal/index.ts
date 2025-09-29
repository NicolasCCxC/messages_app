export { DialogModal } from './DialogModal';
export { Modal } from './Modal';

/**
 * Interface for the Modal component props
 *
 * @typeParam open: boolean - Determines whether the modal is currently open or closed
 * @typeParam onClose: () => void - Function to be called when the modal needs to be closed
 * @typeParam title: string - The title to be displayed at the top of the modal
 * @typeParam children: React.ReactNode - Content to be rendered inside the modal.
 * @typeParam modalClassName: string - Optional prop to apply custom CSS classes to the modal
 * @typeParam onSave:  React.MouseEventHandler<HTMLButtonElement> - Optional prop to handle onClick action in button modal
 * @typeParam noButtons: boolean - Optional flag that indicates if the modal does not have buttons
 * @typeParam saveButtonText: string - Optional prop to set the text of the save button in the modal
 */
export interface IModalProps {
    open: boolean;
    onClose: () => void;
    title: string;
    children: React.ReactNode;
    modalClassName?: string;
    onSave?: React.MouseEventHandler<HTMLButtonElement>;
    noButtons?: boolean;
    saveButtonText?: string;
}

/**
 * This describes the props of the dialog modal
 *
 * @typeParam data: DialogModalData - Optional data displayed in the modal
 * @typeParam onConfirm: () => void - Function executed when clicking the confirm button
 * @typeParam onClose: () => void - Function executed when clicking the cancel button
 * @typeParam type: DialogModalType - Optional modal type
 */
export interface IDialogModalProps {
    data?: DialogModalData;
    onConfirm: () => void;
    onClose: () => void;
    type?: DialogModalType;
}

/**
 * This describes the properties of the dialog modal data
 *
 * @typeParam description: string - Modal description
 * @typeParam rightButtonText: string - Optional right button text
 * @typeParam title: string - Modal text
 */
type DialogModalData = {
    description: string;
    rightButtonText?: string;
    title: string;
};

/**
 * This contains the types of the dialog modal
 */
export enum DialogModalType {
    Delete = 'DELETE',
    Reactivation = 'REACTIVATION',
    ChangeObject = 'CHANGE_OBJECT',
    ChangePage = 'CHANGE_PAGE',
    ReturnObjectPage = 'RETURN_OBJECT_PAGE',
    DeleteObject = 'DELETE_OBJECT',
    ProductChange = 'PRODUCT_CHANGE',
    ExitFormat = 'EXIT_FORMAT',
    CancelProcess = 'CANCEL_PROCESS',
}

/**
 * This contains the data used in each type of dialog modal.
 */
export const DIALOG_MODAL_DATA: { [key in DialogModalType]: DialogModalData } = {
    [DialogModalType.Delete]: {
        title: 'Eliminar',
        description: '¿Está seguro de eliminar este registro?',
        rightButtonText: 'Eliminar',
    },
    [DialogModalType.Reactivation]: {
        title: 'Notificación',
        description: '¿Está seguro de activar este formato?',
        rightButtonText: 'Activar',
    },
    [DialogModalType.ChangeObject]: {
        title: 'Información',
        description: '¿Está seguro de cambiar de objeto? El objeto creado se perderá.',
        rightButtonText: 'Aceptar',
    },
    [DialogModalType.ChangePage]: {
        title: 'Información',
        description: '¿Estás seguro de que desea salir? Puede perder el progreso no guardado.',
        rightButtonText: 'Aceptar',
    },
    [DialogModalType.ReturnObjectPage]: {
        title: 'Información',
        description: '¿Está seguro de volver a la pantalla anterior ? El objeto creado se perderá.',
        rightButtonText: 'Aceptar',
    },
    [DialogModalType.DeleteObject]: {
        title: 'Información',
        description: '¿Está seguro de eliminar este registro?',
        rightButtonText: 'Eliminar',
    },
    [DialogModalType.ProductChange]: {
        title: 'Información',
        description: '¿Está seguro de cambiar de producto? El formato creado se perderá.',
    },
    [DialogModalType.ExitFormat]: {
        title: 'Información',
        description: '¿Está seguro de volver a la pantalla anterior ? \nEl formato PDF creado se perderá.',
    },
    [DialogModalType.CancelProcess]: {
        title: 'Notificación',
        description: '¿Está seguro de cancelar este proceso?',
        rightButtonText: 'Cancelar',
    },
};
