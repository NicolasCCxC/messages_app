import { IBreadcrumbItem } from '@components/breadcrumb';
import { IFields } from '@models/Table';

export { default } from './AuditConsultation';

/**
 * Array of breadcrumb items.
 */
export const BREADCRUMB_ITEMS: IBreadcrumbItem[] = [
    { title: 'Reportes', path: '#' },
    { title: 'Consulta de auditoria', path: '#' },
];

/**
 * Function to parse HTML entities in a string and convert it to a JSON object.
 * @param str - The string containing HTML entities.
 * @returns The parsed JSON object.
 */
export const parseHtmlEntitiesToJson = (str: string): string => {
    const parser = new DOMParser();
    const decodedStr = parser.parseFromString(str, 'text/html').documentElement.textContent;

    return decodedStr ?? '';
};

/**
 * These are the fields of the table
 */
export const TABLE_FIELDS: IFields = {
    header: [
        {
            value: 'Fecha',
            className: 'w-[7.5rem]',
        },
        {
            value: 'Usuario',
            className: 'w-[10.05rem]',
        },
        {
            value: 'IP',
            className: 'w-[10.05rem]',
        },
        {
            value: 'Acción realizada',
            className: 'w-[10.05rem]',
        },
        {
            value: 'Valor anterior',
            className: 'w-[10.05rem]',
        },
        {
            value: 'Valor nuevo',
            className: 'w-[10.05rem]',
        },
    ],
    body: [
        {
            name: 'createdAt',
        },
        {
            name: 'userName',
        },
        {
            name: 'ip',
        },
        {
            name: 'action',
        },
        {
            name: 'prevValue',
        },
        {
            name: 'newValue',
        },
    ],
};

/**
 * Mock data for the table.
 */
export const MOCK_DATA = Array.from({ length: 10 }, (_, index) => ({
    id: index + 1,
    date: `2023-10-${String(index + 1).padStart(2, '0')}`,
    user: `user${index + 1}`,
    IP: `192.168.1.${index + 1}`,
    actionTaken: `Action ${index + 1}`,
    previousValue: Math.floor(Math.random() * 100),
    newValue: Math.floor(Math.random() * 100),
}));
