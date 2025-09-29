import { IBreadcrumbItem } from '@components/breadcrumb';
import type { IOption } from '@components/select-search';
import type { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';

export { default } from './ManageContentProduct';

/**
 * This interface represents the state of an individual field
 *
 * @typeParam isFixed: boolean - Indicates if the field is fixed and cannot be edited
 * @typeParam content: string | null - The content of the field, or null if empty
 * @typeParam inputProductStructureId: string | null - Identifier of the associated product structure, or null
 * @typeParam id: string - Optional id
 */
export interface IFieldState {
    isFixed: boolean;
    content: string | null;
    inputProductStructureId: string | null;
    id?: string;
}

/**
 * This interface defines the props required for managing the Create/Edit Data Modal component.
 *
 * @typeParam toggleModal: () => void - Function to open or close the modal
 * @typeParam toggleToast: () => void - Function to show or hide a toast notification
 * @typeParam handleMessageToast: (message: string) => void - Function to set the toast message content
 * @typeParam products: IOption[] - List of product options available for selection
 * @typeParam modifyData: IGenericRecord - Object representing the data to be modified (used in edit mode)
 * @typeParam isModify: boolean - Flag to indicate if the modal is in modify (edit) mode
 * @typeParam handleUpdateData: (editRow: IGenericRecord[]) => void  - Function to update data for table
 */
export interface ICreateDataModal {
    toggleModal: () => void;
    toggleToast: () => void;
    handleMessageToast: (message: string) => void;
    products: IOption[];
    modifyData: IGenericRecord;
    isModify: boolean;
    handleUpdateData: (editRow: IGenericRecord[]) => void;
}

/**
 * This interface describes the props used for managing required fields
 *
 * @typeParam sendModal: boolean - Indicate if modal was send
 * @typeParam fields: FieldState[] - List of field states to be rendered or managed
 * @typeParam updateField: (index: number, field: Partial<FieldState>) => void - Function to update a field by index
 * @typeParam onAddField: () => void - Function to add a new field
 * @typeParam options: IOption[] - List of selectable options for fields
 */
export interface RequiredFieldsProps {
    sendModal: boolean;
    fields: IFieldState[];
    updateField: (index: number, field: Partial<IFieldState>) => void;
    onAddField: () => void;
    options: IOption[];
}

/**
 * This returns the fields of the table
 *
 * @param products: IGenericRecord[] - Product list
 * @returns IFields
 */
export const getTableFields = (allProducts: IGenericRecord[]): IFields => ({
    header: [
        {
            value: 'Producto',
            className: 'w-[15.625rem]',
        },
        {
            value: 'Campos requeridos',
            className: 'w-[16.875rem]',
            icon: 'exclamationWhite',
        },
        {
            value: 'Tipo de archivo',
            className: 'w-[9.375rem]',
        },
        {
            value: 'Nombre archivo de índice',
            className: 'w-[9.375rem] leading-[0.9375rem]',
        },
    ],
    body: [
        {
            name: 'product',
            type: FieldType.Select,
            options: allProducts as IOption[],
        },
        {
            name: 'mappedRequiredFields',
            isCustom: true,
        },
        {
            name: 'typeFile',
        },
        {
            name: 'nameIndexFile',
            isCustom: true,
        },
        {
            name: '',
            type: FieldType.Icons,
            icons: ['pencilBlue', 'trashBlue'],
        },
    ],
    required: ['code', 'description', 'documentType'],
});

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Gestión del contenido del archivo de índice por producto', path: '#' },
];

/**
 * Object of default value for input select in modal
 */
export const DEFAULT_SELECT = { label: '', value: '' };

/**
 * Array of default object values for file input select
 */
export const FILE_OPTIONS: IOption[] = [
    { label: 'CSV', value: 'CSV' },
    { label: 'TXT', value: 'TXT' },
];

/**
 * These are the max length for fields
 */
export const MAX_LENGHT_FIELD = {
    nameIndexFile: 50,
    fixedRequiredfile: 1000,
};

/**
 * Object of default value for required select
 */
export const DEFAULT_REQUIRED_VALUES = { isFixed: false, inputProductStructureId: null, content: null };
