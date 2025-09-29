import { IOption } from '@components/multi-select';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { LETTERS_AND_SPACE_REGEX } from '@constants/Text';
import { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';

export { UserModal } from './UserModal';
export { default } from './UserManagement';

/**
 * This is all the data in the table without pagination
 */
export const ALL_DATA = [
    {
        id: '53f8ebec-e1b4-46ee-9f0d-4ae8c289cb93',
        email: 'elpo@yito.com',
        name: 'John Breyner Londono Lopez',
        active: false,
        status: 'Inactivo',
        blocked: false,
        roles: [
            {
                id: 'd39b643a-c30d-4254-aab2-01709f8076b5',
                code: 1,
                description: 'ADMINISTRADOR',
                active: true,
                createdAt: '2025-01-29T17:02:02.00391',
                updateAt: '2025-01-29T17:02:02.00391',
            },
        ],
        createdAt: '2025-01-29T17:02:40.743546',
        updateAt: '2025-01-29T17:02:40.743546',
    },
    {
        id: 'fa0fc58b-5ac0-475c-a94c-25840678d8d9',
        email: 'dia@nita.com',
        name: 'Diana Valdivieso',
        status: 'Activo',
        active: true,
        blocked: false,
        roles: [
            {
                id: 'd39b643a-c30d-4254-aab2-01709f8076b5',
                code: 1,
                description: 'ADMINISTRADOR',
                active: true,
                createdAt: '2025-01-29T17:02:02.00391',
                updateAt: '2025-01-29T17:02:02.00391',
            },
            {
                id: '323ce3ff-8203-444a-808a-f3d85e4c0132',
                code: 2,
                description: 'ESCRITURA',
                active: true,
                createdAt: '2025-01-29T17:02:02.051613',
                updateAt: '2025-01-29T17:02:02.051613',
            },
        ],
        createdAt: '2025-01-29T17:04:31.213626',
        updateAt: '2025-01-29T17:04:31.213626',
    },
];

/**
 * This returns the fields of the table
 *
 * @param roles: IGenericRecord[] - roles of user
 * @returns IFields
 */
export const getTableFields = (roles: IGenericRecord[]): IFields => {
    return {
        header: [
            {
                value: 'Usuario de red',
                className: 'w-[16.875rem]',
            },
            {
                value: 'Nombres y apellidos',
                className: 'w-[18.75rem]',
            },
            {
                value: 'Rol o roles',
                className: 'w-[11.375rem]',
            },
            {
                value: 'Estado',
                className: 'w-[7.5rem]',
            },
            {},
        ],
        body: [
            {
                name: 'email',
                maxLength: MAX_LENGHT.email,
            },
            {
                name: 'userName',
                validatePattern: LETTERS_AND_SPACE_REGEX,
                maxLength: MAX_LENGHT.name,
            },
            {
                name: 'roles',
                type: FieldType.MultiSelect,
                multiSelectOptions: roles,
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
        required: ['email', 'userName', 'roles', 'active'],
    };
};

/**
 * Breadcrumb routes
 */
export const ROUTES = [
    { title: 'Parámetros de seguridad', path: '#' },
    { title: 'Gestión de usuarios', path: '#' },
];

/**
 * Object of default values for input text in modal
 */
export const DEFAULT_FORM_VALUES = {
    email: '',
    name: '',
};

/**
 * Object of default value for input select role in modal
 */
export const DEFAULT_SELECT_ROL = { id: '', value: 'ADMINISTRADOR', active: true };

/**
 * Object of default value for input select state in modal
 */
export const DEFAULT_SELECT_STATE = { label: 'Activo', value: 'true' };

/**
 * Object of fake data for multi select
 */
export const options: IOption[] = [
    { description: 'Administrador', code: '1' },
    { description: 'Escritura', code: '2' },
    { description: 'Lectura', code: '3' },
];

/**
 * Max lenght for modal inputs
 */
export const MAX_LENGHT = { email: 320, name: 100 };
