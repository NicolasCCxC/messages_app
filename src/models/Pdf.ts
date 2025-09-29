import type { ElementType as ObjectType } from '@constants/ObjectsEditor';
import { FIELD } from '@constants/Pdf';

import { IElement } from '@pages/object-manage-format/context';

/**
 * Props for components that visually represent a single PDF element
 *
 * @typeParam element: IElement - A visual entity (e.g. text, image, shape) placed on the PDF canvas
 */
export interface IPdfObject {
    element: IElement;
}

/**
 * Type representing both predefined object types and the literal FIELD type
 */
export type ExtendedObjectType = ObjectType | typeof FIELD;
