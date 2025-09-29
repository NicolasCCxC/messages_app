import { IBreadcrumbItem } from '@components/breadcrumb';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { ALPHA_NUMERIC_REGEX } from '@constants/Text';
import { FieldType, IFields } from '@models/Table';

export { default } from './ProductManagement';

/**
 * Interface for the Product Management object
 *
 * @typeParam code: string - The unique identifier for the product
 * @typeParam description: string - A brief description of the product
 * @typeParam documentType: string - The type of document associated with the product
 * @typeParam active: boolean - Indicates whether the product is active or inactive
 */
export interface IProductManagement {
    code: string;
    description: string;
    documentType: string;
    active: boolean;
}

/**
 * Object of default values for input text in modal
 */
export const DEFAULT_FORM_VALUES = {
    code: '',
    description: '',
    documentType: '',
};

/**
 * Object of default value for input select in modal
 */
export const DEFAULT_SELECT_STATE = { label: '', value: '' };

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Gestión de productos', path: '#' },
];

/**
 * These are the max length for fields
 */
export enum MaxLengthField {
    Description = 100,
    Code = 6,
}

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS:IFields = {
    header: [
        {
            value: 'Código del producto',
            className: 'w-[13.625rem]',
        },
        {
            value: 'Descripción del producto',
            className: 'w-[13.625rem]',
        },
        {
            value: 'DocumentTYpe',
            className: 'w-[13.625rem]',
        },
        {
            value: 'Estado del producto',
            className: 'w-[13.625rem]',
        },
        {},
    ],
    body: [
        {
            name: 'code',
            validatePattern: ALPHA_NUMERIC_REGEX,
            maxLength: MaxLengthField.Code,
        },
        {
            name: 'description',
            maxLength: MaxLengthField.Description,
        },
        {
            name: 'documentType',
            maxLength: MaxLengthField.Description,
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

    required: ['code', 'description', 'documentType', 'active'],
};
