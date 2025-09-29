import { createContext } from 'react';
import { ElementType, ObjectType } from '@constants/ObjectsEditor';
import { IGenericRecord } from '@models/GenericRecord';

/**
 * Initial state for an editor element.
 * This object provides default values for a new element before any user configuration.
 */
export const initialElementState: IElement = {
    productId: '',
    name: '',
    identifier: '',
    objectType: ObjectType.Generic,
    type: '',
};

/**
 * This describes the structure of a generic editor object
 *
 * @typeParam productId: string - Unique identifier for the product
 * @typeParam name: string - Name of the object
 * @typeParam identifier: string - Secondary identifier for internal use
 * @typeParam objectType: ObjectType - Type of the object
 * @typeParam type: string - Content type
 * @typeParam id: string - Optional id of element
 * @typeParam image: string - Optional image url
 * @typeParam content: string - Optional content or reference (e.g., URL or text)
 * @typeParam style: React.CSSProperties - Optional style configuration for the element
 * @typeParam header: IGenericRecord - Optional structure data for header table
 * @typeParam body: IGenericRecord - Optional style configuration for the element
 */
export interface IElement {
    productId: string;
    name: string;
    identifier: string;
    objectType: ObjectType;
    type: string;
    id?: string;
    image?: string;
    content?: string;
    style?: React.CSSProperties;
    header?: IGenericRecord;
    body?: IGenericRecord;
}

/**
 * This describes the context for managing editor elements
 *
 * @typeParam selectedElementType: ElementType | null - Currently selected element type in the editor
 * @typeParam handleClickElement: (elementType: ElementType) => void - Handles selection of an element from the sidebar
 * @typeParam updateElementProperties: (name: Exclude<keyof IElement, "style">, value: string | number) => void - Update element properties
 * @typeParam updateElementStyles: (name: keyof React.CSSProperties, value: string | number) => void - Update element styles
 * @typeParam element: IElement - Element to create
 * @typeParam setElement:  React.Dispatch<React.SetStateAction<IElement>> - setState to modify element
 * @typeParam setSelectedElementType:  React.Dispatch<React.SetStateAction<ElementType | null> - setState to modify elementType
 */
export interface IManageObjectContext {
    selectedElementType: ElementType | null;
    handleClickElement: (elementType: ElementType) => void;
    updateElementProperties: (name: Exclude<keyof IElement, 'style'>, value: string | number) => void;
    updateElementStyles: (name: keyof React.CSSProperties, value: string | number) => void;
    element: IElement;
    setElement: React.Dispatch<React.SetStateAction<IElement>>;
    setSelectedElementType: React.Dispatch<React.SetStateAction<ElementType | null>>;
}

/**
 * Maps each element type to its corresponding object type.
 */
export const ELEMENT_TYPE_TO_OBJECT_TYPE_MAP: Record<string, ObjectType> = {
    [ElementType.Table]: ObjectType.Table,
    [ElementType.Image]: ObjectType.Image,
};

/**
 * This is the context
 */
export const ManageObjectContext = createContext<IManageObjectContext>({} as IManageObjectContext);
