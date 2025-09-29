/* eslint-disable @typescript-eslint/no-explicit-any */
import { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IBodyField } from '@models/Table';
import { Input, Select, MultiSelect } from './Input';

export { Body } from './Body';
export { Header } from './Header';
export { Icons } from './Icons';
export { Input, Select } from './Input';
export { Paginator } from './Paginator';

/**
 * Dynamic interface for data in each item
 *
 * @typeParam [key: string]: any - dynamic property that allows passing different values to item
 */
export interface IItem extends IBodyField {
    [key: string]: any;
}

/**
 * This interface describes the field props
 *
 * @typeParam handleChange: (...props: any) => void - Function to handle value changes in fields
 * @typeParam isEditable: boolean - This indicates whether the field is editable
 * @typeParam item: IItem - Item data
 */
export interface IFieldProps {
    handleChange: (...props: any) => void;
    isEditable: boolean;
    item: IItem;
}

/**
 * This interface describes the props for the body component
 *
 * @typeParam fields: IBodyField[] - Body fields
 * @typeParam onFieldChange: (value: string { item, row }: IGenericRecord) => void - Function to handle the change of value in a table cell
 * @typeParam requiredFields: string[] - Optional required fields
 * @typeParam customIcons: (item: IGenericRecord) => JSX.Element - Optional component that has custom icons for the table
 */
export interface IBodyProps {
    fields: IBodyField[];
    onFieldChange: (value: string, { item, row }: IGenericRecord) => void;
    requiredFields?: string[];
    customIcons?: (item: IGenericRecord) => JSX.Element;
}

/**
 * This contains the types of fields
 */
export const FIELD_TYPES: { [key in Exclude<FieldType, FieldType.Icons>]: React.FC<IFieldProps> } = {
    [FieldType.Number]: Input,
    [FieldType.Text]: Input,
    [FieldType.Select]: Select,
    [FieldType.MultiSelect]: MultiSelect,
};
