import { IBreadcrumbItem } from '@components/breadcrumb';
import { IOption } from '@components/select-search';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';

export { default } from './PathsDataFiles';

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Rutas para archivos de datos', path: '#' },
];

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS = (allProducts: IOption[]): IFields => ({
    header: [
        {
            value: 'Producto',
            className: 'w-[13.625rem]',
        },
        {
            value: 'Ruta archivo entrada',
            className: 'w-[13.625rem]',
        },
        {
            value: 'Ruta archivos procesados',
            className: 'w-[13.625rem]',
        },
        {
            value: 'Estado',
            className: 'w-[13.625rem]',
        },
        {},
    ],
    body: [
        {
            name: 'product',
            type: FieldType.Select,
            options: allProducts,
        },
        {
            name: 'routeEntry',
            maxLength: MaxLengthField.Path,
        },
        {
            name: 'routeProcessed',
            maxLength: MaxLengthField.Path,
        },
        {
            name: 'active',
            type: FieldType.Select,
            options: DEFAULT_STATE_OPTIONS,
        },
        {
            name: '',
            type: FieldType.Icons,
            icons: ['pencilBlue'],
        },
    ],

    required: ['product', 'routeEntry', 'routeProcessed', 'active'],
});

/**
 * Object of default value for input select in modal
 */
export const DEFAULT_SELECT_PRODUCT = { label: '', value: '' };

/**
 * Object of default value for input select in modal
 */
export const DEFAULT_SELECT_STATE = { label: '', value: '' };

/**
 * These are the max length for fields
 */
export enum MaxLengthField {
    Path = 200,
}

/**
 * Object of default values for input text in modal
 */
export const DEFAULT_FORM_VALUES: IGenericRecord = {
    id: null,
    routeEntry: '',
    routeProcessed: '',
};
