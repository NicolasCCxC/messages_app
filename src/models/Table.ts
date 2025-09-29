import { IconName } from '@components/icon';
import { IOption } from '@components/select-search';
import { IGenericRecord } from './GenericRecord';

/**
 * Possible field types for table fields are described here
 */
export enum FieldType {
    Text = 'TEXT',
    Number = 'NUMBER',
    Select = 'SELECT',
    Icons = 'ICONS',
    MultiSelect = 'MULTI_SELECT',
}

/**
 * This interface describes the body field props
 *
 * @typeParam name: string - The name of the field
 * @typeParam type: FieldType - Optional type of the field (e.g., Text, Number, Select)
 * @typeParam options: { value: string | boolean; label: string }[] - An array of optional options for fields of type 'Select'
 * @typeParam icons: IconName[] - An optional list of icons to interact with the table fields
 * @typeParam editable: boolean - Optional flag indicating whether the field is editable
 * @typeParam activeItem: boolean - Optional flag indicating whether the field is required
 * @typeParam maxLength: number - This is optional and indicates the maximum length of the field
 * @typeParam isCustom: boolean - Optional flag indicating whether the field is custom
 * @typeParam availableOptions: IOption[] - List of available options
 * @typeParam multiSelectOptions: IGenericRecord[] - Optional multi select options for fields of type 'MultiSelect'
 * @typeParam validatePattern: RegExp - Optional regular expression pattern for validation
 */
export interface IBodyField {
    name: string;
    type?: FieldType;
    options?: IOption[];
    icons?: IconName[];
    editable?: boolean;
    activeItem?: boolean;
    maxLength?: number;
    isCustom?: boolean;
    availableOptions?: IOption[];
    multiSelectOptions?: IGenericRecord[];
    validatePattern?: RegExp;
}

/**
 * This interface describes the header field props
 *
 * @typeParam value: string - Optional header value
 * @typeParam className: string - Optional custom class name for styling
 * @typeParam icon: IconName - Optional icon
 */
export interface IHeaderField {
    value?: string;
    className?: string;
    icon?: IconName;
}

/**
 * This interface describes the table fields
 *
 * @typeParam body: IBodyField[] - Fields of the body
 * @typeParam header: IHeaderField[] - Header fields
 * @typeParam required: string[] - Required fields
 */
export interface IFields {
    body: IBodyField[];
    header: IHeaderField[];
    required?: string[];
}

/**
 * This describes the properties that make up the data
 *
 * @typeParam all: IGenericRecord[] - That is all the data in the table without pagination
 * @typeParam current: IGenericRecord[] - This is the data currently displayed in the table
 * @typeParam update: (data: IGenericRecord[]) => void - Function to update table data
 * @typeParam pages: number - Table pages
 */
export interface IData {
    all: IGenericRecord[];
    current: IGenericRecord[];
    update: (data: IGenericRecord[]) => void;
    pages: number;
}

/**
 * This interface defines the structure for managing search functionality in a table
 *
 * @typeParam showMessage: boolean - Flag to indicate whether the search message should be displayed
 * @typeParam value: string - Search value
 */
export interface ISearch {
    showMessage: boolean;
    value: string;
}

/**
 * This describes the props of the table
 *
 * @typeParam data: IData - This groups everything related to the data in the table
 * @typeParam editing: IEditing - This contains the functions used to edit the data
 * @typeParam fields: IFields - Fields of the header and body to build the table
 * @typeParam search: ISearch - This contains the properties related to data search
 * @typeParam customIcons: (item: IGenericRecord) => JSX.Element - Optional component that has custom icons for the table
 * @typeParam wrapperClassName: string - Optional class name for the table container
 */
export interface ITableProps {
    data: IData;
    fields: IFields;
    editing: IEditing;
    search: ISearch;
    customIcons?: (item: IGenericRecord) => JSX.Element;
    wrapperClassName?: string;
}

/**
 * This describes the set of properties used to edit the data in the table.
 *
 * @typeParam onDeleteRow: (id: string) => void - Optional function used to delete a row from the table
 * @typeParam onFieldChange: (value: string, { item, row }: IGenericRecord) => void - Optional unction to handle the change of value in a table cell
 * @typeParam onPageChange: (page: number, search: string) => void - Optional function to handle the pager
 * @typeParam onUpdateRow: (id: string) => void - This is used to update a row
 * @typeParam onIconClick: (icon: IconName, item: IGenericRecord) => void; - Optional function to execute onClicks on icons
 */
export interface IEditing {
    onDeleteRow?: (id: string) => void;
    onFieldChange?: (value: string, { item, row }: IGenericRecord) => void;
    onPageChange?: (page: number, search: string) => void;
    onUpdateRow?: (id: string) => void;
    onIconClick?: (icon: IconName, item: IGenericRecord) => void;
}
