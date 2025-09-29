import type { IOption } from '@components/select-search';
import { DEFAULT_STATE_OPTIONS } from '@constants/DefaultSelectOptions';
import { FIELD, FONTS, PAPER_SIZES } from '@constants/Pdf';
import type { IGenericRecord } from '@models/GenericRecord';
import { FieldType, IFields } from '@models/Table';
import type { IFormatConfig } from '@pages/pdf-presentation/context';

export { FormatList } from './FormatList';
export { ModalView } from './ModalView';
export { TableIcons } from './TableIcons';

/**
 * Describes the props of the format list
 *
 * @typeParam toggleEditor: () => void - This is used to toggle the editor rendering
 */
export interface IFormatListProps {
    toggleEditor: () => void;
}

/**
 * Describes the props of the custom table icons
 *
 * @typeParam item: IGenericRecord - Row item
 * @typeParam toggleEditor: () => void - Toggles between table view and editor mode
 * @typeParam toggleVisualization: () => void - Toggles the visibility of the editor modal without altering the underlying table
 */
export interface ITableIconsProps {
    item: IGenericRecord;
    toggleEditor: () => void;
    toggleVisualization: () => void;
}

/**
 * This contains the names of the inputs to create records
 */
export enum FieldName {
    Active = 'active',
    ProductId = 'productId',
    Version = 'version',
}

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
            className: 'w-[16rem]',
        },
        {
            value: 'Versión del formato',
            className: 'w-[16rem]',
        },
        {
            value: 'Estado de formato',
            className: 'w-[16rem]',
        },
        {},
    ],
    body: [
        {
            name: FieldName.ProductId,
            type: FieldType.Select,
            options: products as IOption[],
            editable: false,
        },
        {
            name: FieldName.Version,
            editable: false,
        },
        {
            name: FieldName.Active,
            type: FieldType.Select,
            options: DEFAULT_STATE_OPTIONS,
            editable: false,
        },
        {
            name: '',
            type: FieldType.Icons,
            icons: [],
        },
    ],
});

/**
 * Formats page data by extracting and restructuring elements and pages
 *
 * @param pages: IGenericRecord[] - Array of page objects containing elements and their properties
 * @returns { elements: IGenericRecord; pages: string[] }
 */
export const formatPageData = (
    pages: IGenericRecord[],
    pdfMargins: IGenericRecord
): { elements: IGenericRecord; pages: string[] } => {
    const elements: IGenericRecord = {};
    const formattedPages: string[] = [];

    pages.forEach(({ id, objects }) => {
        formattedPages.push(id);
        const modifiedObjects = objects.map(({ positionX: x, positionY: y, ...restObject }: IGenericRecord, index: number) => {
            const id = restObject.type === FIELD ? restObject?.fieldId : restObject?.id;

            const modifiedProduct = {
                ...restObject,
                id: `${id}-${index}`,
                sourceId: id,
                style: restObject?.style,
                x: Math.floor(x + pdfMargins.left),
                y: Math.floor(y + pdfMargins.top),
            };
            return modifiedProduct;
        });
        elements[id] = modifiedObjects;
    });

    return { elements, pages: formattedPages };
};

/**
 * Builds the mat configuration object based on the provided item.
 *
 * @param item - IforGenericRecord
 * @returns IFormatConfig
 */
export const buildFormatConfig = ({
    id,
    productId,
    version,
    pdfConfig: { fontFamily, paperType, margins, continues: isContinue },
}: IGenericRecord): IFormatConfig => ({
    id,
    isNew: false,
    productId,
    version,
    font: { label: fontFamily, value: FONTS.find(({ label }) => label === fontFamily)?.value ?? '' },
    pageSize: { label: paperType, value: PAPER_SIZES.find(({ label }) => label === paperType)?.value ?? '' },
    margins,
    continues: isContinue,
});
