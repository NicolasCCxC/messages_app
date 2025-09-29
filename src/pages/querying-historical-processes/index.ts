import { IBreadcrumbItem } from '@components/breadcrumb';
import { IFields } from '@models/Table';

export { default } from './QueryingHistoricalProcesses';

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Reportes', path: '#' },
    { title: 'Consulta de históricos de proceso de extractos', path: '#' },
];

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS: IFields = {
    header: [
        {
            value: 'Producto',
            className: 'w-[14.4375rem]',
        },
        {
            value: 'Fecha',
            className: 'w-[14.4375rem]',
        },
        {
            value: 'Usuario',
            className: 'w-[14.4375rem]',
        },
        {
            value: 'Cantidad de extractos',
            className: 'w-[14.4375rem]',
        },
    ],
    body: [
        {
            name: 'productName',
        },
        {
            name: 'date',
        },
        {
            name: 'user',
        },
        {
            name: 'details',
        },
    ],
};

/**
 * Mock data for the table.
 */
export const MOCK_DATA = [
    { id: 1, code: 'Producto A', date: '2023-10-01', user: 'Usuario A', extractAmount: 5 },
    { id: 2, code: 'Producto B', date: '2023-10-02', user: 'Usuario B', extractAmount: 3 },
    { id: 3, code: 'Producto C', date: '2023-10-03', user: 'Usuario C', extractAmount: 8 },
    { id: 4, code: 'Producto D', date: '2023-10-04', user: 'Usuario D', extractAmount: 2 },
    { id: 5, code: 'Producto E', date: '2023-10-05', user: 'Usuario E', extractAmount: 7 },
    { id: 6, code: 'Producto F', date: '2023-10-06', user: 'Usuario F', extractAmount: 1 },
    { id: 7, code: 'Producto G', date: '2023-10-07', user: 'Usuario G', extractAmount: 4 },
    { id: 8, code: 'Producto H', date: '2023-10-08', user: 'Usuario H', extractAmount: 6 },
    { id: 9, code: 'Producto I', date: '2023-10-09', user: 'Usuario I', extractAmount: 9 },
    { id: 10, code: 'Producto J', date: '2023-10-10', user: 'Usuario J', extractAmount: 10 },
];
