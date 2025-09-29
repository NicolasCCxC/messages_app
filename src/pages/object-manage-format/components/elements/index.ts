import { IElement } from '@pages/object-manage-format/context';

export { Text } from './Text';
export { Image } from './Image';
export { Table } from './table';
export { Shape } from './Shape';

/**
 * This describes an object wrapper for an editor element with optional scaling information.
 *
 * @typeParam element: IElement - The core element data (e.g., text, image, table)
 * @typeParam isPdfMode: boolean - Optional flag indicating if the element is in PDF mode (default is false)
 * @typeParam isPreviewMode: boolean - Optional flag indicating if the element is in preview mode (default is false)
 */
export interface IObjectElement {
    element: IElement;
    isPdfMode?: boolean;
    isPreviewMode?: boolean;
}

/**
 * Default image size used when no custom size is specified.
 */
export const DEFAULT_IMAGE_SIZE = { width: 300, height: 150 };
