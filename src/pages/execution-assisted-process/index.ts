import { IBreadcrumbItem } from '@components/breadcrumb';
import { IFields } from '@models/Table';

export { default } from './ExecutionAssistedProcess';

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Generación de extractos', path: '#' },
    { title: 'Ejecución del Proceso de Generación de Extractos Asistido', path: '#' },
];

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS = (): IFields => ({
    header: [
        {
            value: 'Producto',
            className: 'w-[9.625rem]',
        },
        {
            value: 'Fecha',
            className: 'w-[9.625rem]',
        },
        {
            value: 'Usuario',
            className: 'w-[9.625rem]',
        },
        {
            value: 'Estado',
            className: 'w-[9.625rem]',
        },
        {
            value: '% avance',
            className: 'w-[9.625rem]',
        },
        {
            value: 'Periodo',
            className: 'w-[9.625rem]',
        },
    ],
    body: [
        {
            name: 'product',
        },
        {
            name: 'date',
        },
        {
            name: 'user',
        },
        {
            name: 'status',
        },
        {
            name: 'percentAdvance',
        },
        {
            name: 'period',
        },
    ],
});

/**
 * Mock data for the table.
 */
export const MOCK_DATA = [
    {
        id: 1,
        product: 'Producto A',
        date: '2023-01-01',
        user: 'Usuario 1',
        active: 'Activo',
        progress: 75,
        period: '2023-01',
    },
    {
        id: 2,
        product: 'Producto B',
        date: '2023-01-02',
        user: 'Usuario 2',
        active: 'Inactivo',
        progress: 50,
        period: '2023-01',
    },
    {
        id: 3,
        product: 'Producto C',
        date: '2023-01-03',
        user: 'Usuario 3',
        active: 'Activo',
        progress: 100,
        period: '2023-01',
    },
    {
        id: 4,
        product: 'Producto D',
        date: '2023-01-04',
        user: 'Usuario 4',
        active: 'Activo',
        progress: 25,
        period: '2023-01',
    },
    {
        id: 5,
        product: 'Producto E',
        date: '2023-01-05',
        user: 'Usuario 5',
        active: 'Inactivo',
        progress: 0,
        period: '2023-01',
    },
    {
        id: 6,
        product: 'Producto F',
        date: '2023-01-06',
        user: 'Usuario 6',
        active: 'Activo',
        progress: 10,
        period: '2023-01',
    },
    {
        id: 7,
        product: 'Producto G',
        date: '2023-01-07',
        user: 'Usuario 7',
        active: 'Inactivo',
        progress: 20,
        period: '2023-01',
    },
    {
        id: 8,
        product: 'Producto H',
        date: '2023-01-08',
        user: 'Usuario 8',
        active: 'Activo',
        progress: 30,
        period: '2023-01',
    },
    {
        id: 9,
        product: 'Producto I',
        date: '2023-01-09',
        user: 'Usuario 9',
        active: 'Activo',
        progress: 40,
        period: '2023-01',
    },
    {
        id: 10,
        product: 'Producto J',
        date: '2023-01-10',
        user: 'Usuario 10',
        active: 'Inactivo',
        progress: 60,
        period: '2023-01',
    },
];
