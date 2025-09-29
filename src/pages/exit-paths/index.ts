import type { IOption } from '@components/select-search';
import type { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';

export { CreateRecordModal } from './CreateRecordModal';
export { default } from './ExitPaths';

/**
 * Maximum allowed length for both outbound path fields
 */
export const MAX_OUTBOUND_PATH_LENGTH = 200;

/**
 * This returns the fields of the table
 *
 * @param products: IGenericRecord[] - Product list
 * @param availableProducts: IGenericRecord[] - List of available options
 * @returns IFields
 */
export const getTableFields = (products: IGenericRecord[], availableProducts: IGenericRecord[]): IFields => ({
    header: [
        {
            value: 'Producto',
            className: 'w-[17.0831rem]',
        },
        {
            value: 'Ruta salida extracto',
            className: 'w-[17.0831rem]',
        },
        {
            value: 'Ruta salida archivos de índices',
            className: 'w-[17.0831rem]',
        },
        {},
    ],
    body: [
        {
            name: 'productId',
            type: FieldType.Select,
            options: products as IOption[],
            availableOptions: availableProducts as IOption[],
        },
        {
            name: 'routeOutputExtract',
            maxLength: MAX_OUTBOUND_PATH_LENGTH,
        },
        {
            name: 'routeOutputIndex',
            maxLength: MAX_OUTBOUND_PATH_LENGTH,
        },
        {
            name: '',
            type: FieldType.Icons,
            icons: ['pencilBlue', 'trashBlue'],
        },
    ],
    required: REQUIRED_FIELDS,
});

/**
 * Required fields to create or update a record
 */
export const REQUIRED_FIELDS = ['productId', 'routeOutputExtract', 'routeOutputIndex'];

/**
 * Breadcrumb routes
 */
export const ROUTES = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Rutas para extractos y archivos de índices', path: '#' },
];

/**
 * Default value of the modal to create records
 */
export const DEFAULT_FORM_VALUES = {
    productId: '',
    routeOutputExtract: '',
    routeOutputIndex: '',
    option: '',
};
