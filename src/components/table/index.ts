import { FieldType, IBodyField, IHeaderField } from '@models/Table';

export { Table } from './Table';

/**
 * Fake data used to display table
 */
export const TABLE_DATA = [
    {
        id: 1,
        code: 'Código de rol',
        register: '1',
        fieldName: 'Descripción name',
        startPosition: 1,
        finalPosition: 3,
        identifier: 6,
    },
    {
        id: 2,
        code: 'Código de rol2',
        register: '',
        fieldName: 'Descripción name2',
        startPosition: 10,
        finalPosition: 30,
        identifier: 60,
    },
    {
        id: 3,
        code: 'Código de rol3',
        register: '',
        fieldName: 'Descripción name3',
        startPosition: 20,
        finalPosition: 40,
        identifier: 80,
    },
    {
        id: 4,
        code: 'Código de rol4',
        register: '1',
        fieldName: 'Descripción name',
        startPosition: 1,
        finalPosition: 3,
        identifier: 6,
    },
    {
        id: 5,
        code: 'Código de rol5',
        register: '',
        fieldName: 'Descripción name2',
        startPosition: 10,
        finalPosition: 30,
        identifier: 60,
    },
    {
        id: 6,
        code: 'Código de rol6',
        register: '',
        fieldName: 'Descripción name3',
        startPosition: 20,
        finalPosition: 40,
        identifier: 80,
    },
    {
        id: 7,
        code: 'Código de rol7',
        register: '1',
        fieldName: 'Descripción name',
        startPosition: 1,
        finalPosition: 3,
        identifier: 6,
    },
    {
        id: 8,
        code: 'Código de rol8',
        register: '',
        fieldName: 'Descripción name2',
        startPosition: 10,
        finalPosition: 30,
        identifier: 60,
    },
    {
        id: 9,
        code: 'Código de rol9',
        register: '',
        fieldName: 'Descripción name3',
        startPosition: 20,
        finalPosition: 40,
        identifier: 80,
    },
    {
        id: 10,
        code: 'Código de rol10',
        register: '1',
        fieldName: 'Descripción name',
        startPosition: 1,
        finalPosition: 3,
        identifier: 6,
    },
    {
        id: 11,
        code: 'Código de rol11',
        register: '',
        fieldName: 'Descripción name2',
        startPosition: 10,
        finalPosition: 30,
        identifier: 60,
    },
    {
        id: 12,
        code: 'Código de rol12',
        register: '',
        fieldName: 'Descripción name3',
        startPosition: 20,
        finalPosition: 40,
        identifier: 80,
    },
];

/**
 * Fake data with table rows
 */
export const ROWS: IBodyField[] = [
    {
        name: 'code',
    },
    {
        name: 'register',
        type: FieldType.Select,
        options: [
            {
                value: '1',
                label: 'Registro 1',
            },
            {
                value: '2',
                label: 'Registro 2',
            },
            {
                value: '3',
                label: 'Registro 3',
            },
        ],
    },
    {
        name: 'fieldName',
    },
    {
        name: 'startPosition',
        type: FieldType.Number,
    },
    {
        name: 'finalPosition',
        type: FieldType.Number,
    },
    {
        name: 'identifier',
    },
    {
        name: '',
        type: FieldType.Icons,
    },
];

/**
 * Fake data with table headers
 */
export const HEADERS: IHeaderField[] = [
    {
        value: 'Producto',
        className: 'w-[12.5rem]',
    },
    {
        value: 'Tipo de registro',
        className: 'w-[10.625rem]',
    },
    {
        value: 'Nombre del campo',
        className: 'w-[10.625rem]',
    },
    {
        value: 'Posición inicial',
        className: 'w-[5rem]',
    },
    {
        value: 'Posición final',
        className: 'w-[5rem]',
    },
    {
        value: 'Identificador',
        className: 'w-[7.5rem]',
    },
    {},
];
