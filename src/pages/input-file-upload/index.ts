import { IBreadcrumbItem } from '@components/breadcrumb';
import { FieldType, IFields } from '@models/Table';

export { default } from './InputFileUpload';

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Generación de extractos', path: '#' },
    { title: 'Cargue de archivo de entrada', path: '#' },
];

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS: IFields = {
    header: [
        {
            value: 'Producto',
            className: 'w-[11.025rem]',
        },
        {
            value: 'Fecha',
            className: 'w-[11.025rem]',
        },
        {
            value: 'Usuario',
            className: 'w-[11.025rem]',
        },
        {
            value: 'Estado',
            className: 'w-[11.025rem]',
        },
        {
            value: '% avance',
            className: 'w-[11.025rem]',
        },
        {},
    ],
    body: [
        {
            name: 'productName',
        },
        {
            name: 'createdAt',
        },
        {
            name: 'userName',
        },
        {
            name: 'status',
        },
        {
            name: 'percentAdvance',
        },
        {
            name: '',
            type: FieldType.Icons,
            icons: [],
        },
    ],
};

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
    },
    {
        id: 2,
        product: 'Producto B',
        date: '2023-01-02',
        user: 'Usuario 2',
        active: 'Inactivo',
        progress: 50,
    },
    {
        id: 3,
        product: 'Producto C',
        date: '2023-01-03',
        user: 'Usuario 3',
        active: 'Activo',
        progress: 100,
    },
    {
        id: 4,
        product: 'Producto D',
        date: '2023-01-04',
        user: 'Usuario 4',
        active: 'Activo',
        progress: 25,
    },
    {
        id: 5,
        product: 'Producto E',
        date: '2023-01-05',
        user: 'Usuario 5',
        active: 'Inactivo',
        progress: 0,
    },
    {
        id: 6,
        product: 'Producto F',
        date: '2023-01-06',
        user: 'Usuario 6',
        active: 'Activo',
        progress: 10,
    },
    {
        id: 7,
        product: 'Producto G',
        date: '2023-01-07',
        user: 'Usuario 7',
        active: 'Inactivo',
        progress: 20,
    },
    {
        id: 8,
        product: 'Producto H',
        date: '2023-01-08',
        user: 'Usuario 8',
        active: 'Activo',
        progress: 30,
    },
    {
        id: 9,
        product: 'Producto I',
        date: '2023-01-09',
        user: 'Usuario 9',
        active: 'Activo',
        progress: 40,
    },
    {
        id: 10,
        product: 'Producto J',
        date: '2023-01-10',
        user: 'Usuario 10',
        active: 'Inactivo',
        progress: 60,
    },
];

// Status constants for the input file upload process
export const statusActive = "ACTIVO";
