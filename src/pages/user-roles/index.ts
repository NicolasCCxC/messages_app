import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { FieldType, IFields } from '@models/Table';

export { default } from './UserRoles';

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS: IFields = {
    header: [
        {
            value: 'Código de rol',
            className: 'w-[18.1669rem]',
        },
        {
            value: 'Descripción de rol',
            className: 'w-[18.1669rem]',
        },
        {
            value: 'Estado de rol',
            className: 'w-[18.1669rem]',
        },
        {},
    ],
    body: [
        {
            name: 'code',
            editable: false,
        },
        {
            name: 'description',
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
    required: ['description'],
};

/**
 * Breadcrumb routes
 */
export const ROUTES = [
    { title: 'Parámetros de seguridad', path: '#' },
    { title: 'Gestión de roles de usuario', path: '#' },
];

/**
 * Default role
 */
export const DEFAULT_ROLE = { active: false, description: '', id: '' };
