import type { IOption } from '@components/select-search';
import type { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';

export { CreateRecordModal } from './CreateRecordModal';
export { default } from './ProductInput';

/**
 * This contains the names of the inputs to create records
 */
export enum FieldName {
    Product = 'product',
    FieldName = 'fieldName',
    RegistrationIdentifier = 'registrationIdentifier',
    RegistrationName = 'typeRegister',
    InitialPosition = 'initialPosition',
    EndPosition = 'endPosition',
    IndexFileIdentifier = 'indexFileIdentifier',
    FieldType = 'fieldType',
}

/**
 * Breadcrumb routes
 */
export const ROUTES = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Definición de estructura de entrada por producto', path: '#' },
];

/**
 * This returns the fields of the table
 *
 * @param products: IGenericRecord[] - Product list
 * @returns IFields
 */
export const getTableFields = (products: IGenericRecord[]): IFields => ({
    header: [
        {
            value: 'Producto',
            className: 'w-[8rem]',
        },
        {
            value: 'Nombre del campo',
            className: 'w-[6rem]',
        },
        {
            value: 'Identificador del registro',
            className: 'w-[6rem]',
        },
        {
            value: 'Nombre de registro',
            className: 'w-[6rem]',
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
            value: 'Identificador para archivo de índices',
            className: 'w-[8rem]',
        },
        {
            value: 'Tipo',
            className: 'w-[8rem]',
        },
        {},
    ],
    body: [
        {
            name: 'productId',
            type: FieldType.Select,
            options: products as IOption[],
        },
        {
            name: FieldName.FieldName,
            maxLength: FIELD_MAXIMUM_LENGTH[FieldName.FieldName],
        },
        {
            name: FieldName.RegistrationIdentifier,
            maxLength: FIELD_MAXIMUM_LENGTH[FieldName.RegistrationIdentifier],
        },
        {
            name: FieldName.RegistrationName,
            maxLength: FIELD_MAXIMUM_LENGTH[FieldName.RegistrationName],
        },
        {
            name: FieldName.InitialPosition,
            maxLength: FIELD_MAXIMUM_LENGTH[FieldName.InitialPosition],
        },
        {
            name: FieldName.EndPosition,
            maxLength: FIELD_MAXIMUM_LENGTH[FieldName.EndPosition],
        },
        {
            name: FieldName.IndexFileIdentifier,
            maxLength: FIELD_MAXIMUM_LENGTH[FieldName.IndexFileIdentifier],
        },
        {
            name: FieldName.FieldType,
            type: FieldType.Select,
            options: defaultSelectTypeOptions,
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
export const REQUIRED_FIELDS = [
    'productId',
    'typeRegister',
    'fieldName',
    'initialPosition',
    'endPosition',
    'registrationIdentifier',
];

/**
 * This contains the maximum lengths of each input
 */
export const FIELD_MAXIMUM_LENGTH: Record<Exclude<FieldName, FieldName.Product | FieldName.FieldType>, number> = {
    [FieldName.FieldName]: 50,
    [FieldName.RegistrationIdentifier]: 2,
    [FieldName.RegistrationName]: 10,
    [FieldName.InitialPosition]: 5,
    [FieldName.EndPosition]: 5,
    [FieldName.IndexFileIdentifier]: 20,
};

/**
 * default values for creating a product input
 */
export const DEFAULT_INPUT = {
    productId: '',
    typeRegister: '',
    fieldName: '',
    initialPosition: '',
    endPosition: '',
    indexFileIdentifier: '',
    option: '',
    registrationIdentifier: '',
    type: '',
};

/**
 * These are the common properties of text and numeric inputs
 */
export const INPUT_COMMON_PROPS = {
    inputClassName: 'h-[1.5625rem]',
    wrapperClassName: 'w-[13.5625rem] h-[2.8125rem] self-end',
    placeholder: '...',
};

/**
 * This contains the static properties of the inputs
 */
export const INPUT_PROPS: { [key in FieldName]: IGenericRecord } = {
    [FieldName.Product]: {
        label: 'Producto',
        wrapperClassName: 'w-full',
    },
    [FieldName.FieldName]: {
        ...INPUT_COMMON_PROPS,
        name: FieldName.FieldName,
        label: 'Nombre del campo',
        maxLength: FIELD_MAXIMUM_LENGTH[FieldName.FieldName],
    },
    [FieldName.RegistrationIdentifier]: {
        ...INPUT_COMMON_PROPS,
        name: FieldName.RegistrationIdentifier,
        label: 'Identificador del registro',
        maxLength: FIELD_MAXIMUM_LENGTH[FieldName.RegistrationIdentifier],
    },
    [FieldName.RegistrationName]: {
        ...INPUT_COMMON_PROPS,
        name: FieldName.RegistrationName,
        label: 'Nombre de registro',
        maxLength: FIELD_MAXIMUM_LENGTH[FieldName.RegistrationName],
    },
    [FieldName.InitialPosition]: {
        ...INPUT_COMMON_PROPS,
        name: FieldName.InitialPosition,
        label: 'Posición inicial',
        maxLength: FIELD_MAXIMUM_LENGTH[FieldName.InitialPosition],
        type: 'number',
    },
    [FieldName.EndPosition]: {
        ...INPUT_COMMON_PROPS,
        name: FieldName.EndPosition,
        label: 'Posición final',
        maxLength: FIELD_MAXIMUM_LENGTH[FieldName.EndPosition],
        type: 'number',
    },
    [FieldName.IndexFileIdentifier]: {
        ...INPUT_COMMON_PROPS,
        name: FieldName.IndexFileIdentifier,
        label: 'Identificador para archivo de índices',
        maxLength: FIELD_MAXIMUM_LENGTH[FieldName.IndexFileIdentifier],
        wrapperClassName: 'self-end h-full w-[13.5625rem]',
    },
    [FieldName.FieldType]: {
        label: 'Tipo',
        wrapperClassName: 'w-full',
    },
};

/**
 * This contains the default options for the select input
 */
export const defaultSelectTypeOptions: IOption[] = [
    { value: 'date', label: 'Fecha' },
    { value: 'text', label: 'Texto' },
    { value: 'currency', label: 'Moneda' },
    { value: 'percentage', label: 'Porcentaje' },
    { value: 'barcode', label: 'Código de barras' },
];
