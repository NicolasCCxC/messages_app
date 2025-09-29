import { createContext } from 'react';
import { PaperSize } from '@constants/Pdf';
import { IDragAndDropContext } from '@models/DragAndDrop';
import { IToastContext } from '@models/Toast';

export { DragAndDropProvider } from './DragAndDropContext';
export { EditorProvider } from './EditorContext';
export { ToastProvider } from './ToastContext';

/**
 * Context structure for managing editor state and actions
 *
 * @typeParam formatConfig: IFormatConfig - Configuration settings for the editor format
 * @typeParam pages: string[] - Array of page identifiers
 * @typeParam reset: () => void - Resets the context state to its initial values
 * @typeParam updateFormatConfig: (config: IFormatConfig) => void - Updates the format configuration
 * @typeParam updatePages: (newPages: string[]) => void - Updates the list of pages
 */
interface IEditorContext {
    formatConfig: IFormatConfig;
    pages: string[];
    reset: () => void;
    updateFormatConfig: (config: IFormatConfig) => void;
    updatePages: (newPages: string[]) => void;
}

/**
 * This describes the props of the margins
 *
 * @typeParam top: number - Top margin
 * @typeParam bottom: number - Bottom margin
 * @typeParam left: number - Left margin
 * @typeParam right: number - Right margin
 */
export interface IMargins {
    top: number;
    bottom: number;
    left: number;
    right: number;
}

/**
 * This describes the props of the format config
 *
 * @typeParam pageSize: { label: string; value: string } - Page size
 * @typeParam margins: IMargins - PDF margins
 * @typeParam productId: string - Product identifier
 * @typeParam version: string - Version of the format
 * @typeParam continues: boolean - Indicates if the format should continue on the next page
 * @typeParam isNew: boolean - Indicates if the format is newly created, optional
 * @typeParam id: string - Unique identifier of the format, optional
 * @typeParam font: { value: string; label: string } - Selected font
 */
export interface IFormatConfig {
    pageSize: { label: string; value: string };
    margins: IMargins;
    productId: string;
    version: string;
    continues?: boolean;
    isNew?: boolean;
    id?: string;
    font: { value: string; label: string };
}

/**
 * These are the initial settings of the PDF.
 */
export const INITIAL_PDF_SETTINGS: IFormatConfig = {
    pageSize: { label: 'Carta', value: PaperSize.Letter },
    continues: false,
    productId: '',
    margins: {
        top: 2.54,
        bottom: 2.54,
        left: 2.54,
        right: 2.54,
    },
    version: '...',
    isNew: true,
    font: { label: 'Arial', value: 'arial' },
};

/**
 * This is the initial state of the context
 */
const INITIAL_STATE = {
    formatConfig: INITIAL_PDF_SETTINGS,
    pages: [],
    updateFormatConfig: (): void => {},
    updatePages: (): void => {},
    reset: (): void => {},
};

/**
 * Context that provides editor state and actions to PDF editor components
 */
export const EditorContext = createContext<IEditorContext>(INITIAL_STATE);

/**
 * Context that provides drag and drop state and handlers to PDF editor components
 */
export const DragAndDropContext = createContext<IDragAndDropContext>({} as IDragAndDropContext);

/**
 * Context that provides toast state and toggle function to components
 */
export const ToastContext = createContext<IToastContext>({} as IToastContext);
