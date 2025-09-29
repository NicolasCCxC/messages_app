import { IconName } from '@components/icon';
import { Text, Image, Table, Shape, IObjectElement } from './elements';
import { ElementType } from '@constants/ObjectsEditor';
import { IGenericRecord } from '@models/GenericRecord';
import { SetStateAction } from 'react';

export { Editor } from './editor/Editor';

/**
 * This describes the props of the editor component
 *
 * @typeParam toggleEditor: () => void - Function to toggle the editor rendering
 * @typeParam toggleEditor: () => void - Function to toggle the modify editor
 * @typeParam toggleToast: () => void - Function to toggle the toast notification
 * @typeParam setSaveError: () => void - Function to set save error validation
 * @typeParam handleMessageToast: (message: string) => void - Function to show the toast notification message
 * @typeParam isModify: boolean - Flag to indicate if editor is in modify mode
 * @typeParam elementToModify: IGenericRecord - Element to render when is modify mode
 */
export interface IEditorProps {
    toggleEditor: () => void;
    toggleModify: () => void;
    toggleToast: () => void;
    setSaveError: React.Dispatch<SetStateAction<boolean>>;
    handleMessageToast: (message: string) => void;
    isModify: boolean;
    elementToModify: IGenericRecord;
}

/**
 * This describes the structure of each sidebar option
 *
 * @typeParam icon: IconName - Icon to be displayed in the sidebar
 * @typeParam label: string - Text label associated with the icon
 */
export interface ISidebarOption {
    icon: IconName;
    label: string;
}

/**
 * This contains the available options to be shown in the editor sidebar
 */
export const sidebarOptions: { icon: IconName; label: string }[] = [
    {
        icon: 'text',
        label: 'Texto',
    },
    {
        icon: 'image',
        label: 'Imagen',
    },
    {
        icon: 'table',
        label: 'Tabla',
    },
    {
        icon: 'shape',
        label: 'Forma',
    },
];

/**
 * The object is used to render the correct component based on the selected element type.
 * It is commonly used inside the editor when placing or rendering dropped elements.
 */
export const elements: {
    [key: string]: React.FC<IObjectElement>;
} = {
    [ElementType.Text]: Text,
    [ElementType.Image]: Image,
    [ElementType.Table]: Table,
    [ElementType.Shape]: Shape,
};
