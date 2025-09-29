import image from '@assets/icons/image.svg';
import shape from '@assets/icons/shape.svg';
import text from '@assets/icons/text.svg';
import table from '@assets/icons/table.svg';
import { ElementType as ObjectType } from '@constants/ObjectsEditor';
import { TabName } from '@constants/Pdf';

export { FieldsPanel } from './FieldsPanel';
export { FormatPanel } from './FormatPanel';
export { ObjectPanel } from './ObjectPanel';
export { Sidebar } from './Sidebar';

/**
 * This describes the properties of each tab
 *
 * @typeParam className: string - Tab className
 * @typeParam label: string - Visible text for each tab
 * @typeParam name: TabName - This is the name of each tab
 */
export interface ITab {
    className: string;
    label: string;
    name: TabName;
}

/**
 * List of margin fields
 */
export const MARGIN_FIELDS = [
    { label: 'Superior', name: 'top', wrapperClassName: '' },
    { label: 'Inferior', name: 'bottom', wrapperClassName: 'my-1.5' },
    { label: 'Izquierdo', name: 'left', wrapperClassName: '' },
    { label: 'Derecho', name: 'right', wrapperClassName: 'mt-1.5' },
];

/**
 * This has the respective icon for each type of object
 */
export const OBJECT_ICONS: { [key in ObjectType]: string } = {
    [ObjectType.Image]: image,
    [ObjectType.Shape]: shape,
    [ObjectType.Table]: table,
    [ObjectType.Text]: text,
};

/**
 * These are the tabs present in the sidebar for navigation.
 */
export const TABS: ITab[] = [
    {
        name: TabName.Fields,
        label: 'Campos',
        className: 'w-[4.4375rem]',
    },
    {
        name: TabName.Objects,
        label: 'Objetos',
        className: 'w-[4.4375rem]',
    },
    {
        name: TabName.Format,
        label: 'Formato',
        className: 'w-[4.625rem]',
    },
];

/**
 * This key is used to filter fields by name
 */
export const FIELD_NAME = 'fieldName';

/**
 * Regular expression to validate numeric values with up to two decimal places
 */
export const DECIMAL_REGEX = /^-?\d+(\.\d{0,2})?$/;
