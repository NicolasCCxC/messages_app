import { IGenericRecord } from '@models/GenericRecord';

import image from '@assets/icons/image.svg';
import shape from '@assets/icons/shape.svg';
import table from '@assets/icons/table.svg';
import text from '@assets/icons/text.svg';
export interface IObjectElement {
    element: {
        header?: {
            columns: IGenericRecord[];
            globalStyles: {
                borderBottom: string;
            };
        };
        body?: {
            cells: IGenericRecord[];
        };
        style?: IGenericRecord;
    };
    scale?: number;
}

export enum ElementType {
    Text = 'TEXT',
    Image = 'IMAGE',
    Table = 'TABLE',
    Shape = 'SHAPE',
}

export enum ObjectType {
    Generic = 'GENERIC',
    Table = 'TABLE',
    Image = 'IMAGE',
}

export const ELEMENT = 'element';

export const INITIAL_STATE_TABLE = {
    header: {
        columns: [{ label: '', rowIndex: 0, columnIndex: 0, style: {} }],
        globalStyles: {
            borderBottom: '1px solid #000',
        },
    },
    body: {
        cells: [{ content: '', rowIndex: 0, columnIndex: 0, style: {} }],
    },
};

export const PLACEHOLDERS = {
    product: 'Producto',
    select: 'Seleccionar',
    fontSize: '11px',
    shapeStyle: '0',
    sizeControl: '100',
};

export const OBJECT_ICONS: Record<ElementType, string> = {
    [ElementType.Image]: image,
    [ElementType.Shape]: shape,
    [ElementType.Table]: table,
    [ElementType.Text]: text,
};
