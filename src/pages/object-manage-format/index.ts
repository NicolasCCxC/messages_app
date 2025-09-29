import { IBreadcrumbItem } from '@components/breadcrumb';
import type { IOption } from '@components/select-search';
import type { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';

export { default } from './ObjectManageFormat';

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Gestión de objetos del formato por tipo de producto', path: '#' },
];

/**
 * This returns the fields of the table
 *
 * @param allProducts: IGenericRecord[] - Product list
 * @returns IFields
 */
export const getTableFields = (allProducts: IGenericRecord[]): IFields => ({
    header: [
        {
            value: 'Producto',
            className: 'w-[16rem]',
        },
        {
            value: 'Código del objeto',
            className: 'w-[16rem]',
        },
        {
            value: 'Nombre del objeto',
            className: 'w-[16rem]',
        },
    ],
    body: [
        {
            name: 'product',
            type: FieldType.Select,
            options: allProducts as IOption[],
        },
        {
            name: 'identifier',
        },
        {
            name: 'objectName',
        },
        {
            name: '',
            type: FieldType.Icons,
            icons: ['pencilBlue', 'eyeBlue','trashBlue'],
        },
    ],
    required: ['code', 'description', 'documentType'],
});
