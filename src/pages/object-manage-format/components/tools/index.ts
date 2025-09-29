import { CSSProperties } from 'react';
import { ElementType } from '@constants/ObjectsEditor';
import { IOption } from '@components/select-search';
import { IconName } from '@components/icon';
import { ImageTool } from './ImageTool';
import { TableTool } from './TableTool';
import { TextTool } from './TextTool';
import { ShapeTool } from './ShapeTool';
import {
    BoldTextStyle,
    CenteredText,
    DefaultText,
    ItalicTextStyle,
    LeftAlignedText,
    RightAlignedText,
    StrikethroughTextStyle,
    UnderlinedTextStyle,
} from './Icons';

export { SidebarTools } from './SidebarTools';

/**
 * Enum representing text alignment options.
 */
export enum Align {
    Left = 'left',
    Center = 'center',
    Right = 'right',
    Justify = 'justify',
}

/**
 * Enum representing different text styling options.
 */
export enum TextStyle {
    Bold = 'bold',
    Italic = 'italic',
    Underline = 'underline',
    lineThrough = 'line-through',
}

/**
 * The object is used to render the correct tool component based on the selected element type.
 * It is commonly used to display configuration options for each element inside the editor.
 */
export const ELEMENT_TOOLS: { [key: string]: React.FC } = {
    [ElementType.Text]: TextTool,
    [ElementType.Image]: ImageTool,
    [ElementType.Table]: TableTool,
    [ElementType.Shape]: ShapeTool,
};

/**
 * Array of text alignment tools used for styling text elements.
 * Each item contains a value for alignment and its corresponding icon.
 */
export const TEXT_ALIGN_TOOLS = [
    { value: Align.Center, Icon: CenteredText },
    { value: Align.Justify, Icon: DefaultText },
    { value: Align.Left, Icon: LeftAlignedText },
    { value: Align.Right, Icon: RightAlignedText },
];

/**
 * Array of text style tools for modifying text appearance.
 * Each item includes a value, icon, and corresponding CSS style property.
 */
export const TEXT_STYLE_TOOLS = [
    { value: 'bold', Icon: BoldTextStyle, styleValue: 'fontWeight' },
    { value: 'italic', Icon: ItalicTextStyle, styleValue: 'fontStyle' },
    { value: 'underline', Icon: UnderlinedTextStyle, styleValue: 'textDecorationLine' },
    { value: 'line-through', Icon: StrikethroughTextStyle, styleValue: 'textDecorationLine' },
];

/**
 * Array of shape style tools used to adjust the border radius of different corners of a shape element.
 * Each tool is associated with a unique icon and a CSS style property.
 */
export const SHAPE_STYLE_TOOLS: { iconName: IconName; styleValue: keyof CSSProperties }[] = [
    { iconName: 'roundedTopLeft', styleValue: 'borderTopLeftRadius' },
    { iconName: 'roundedTopRight', styleValue: 'borderTopRightRadius' },
    { iconName: 'roundedBottomLeft', styleValue: 'borderBottomLeftRadius' },
    { iconName: 'roundedBottomRight', styleValue: 'borderBottomRightRadius' },
];

/**
 * Predefined font size options to be used in text configuration.
 * Each option contains a value and a label, both representing a font size in pixels.
 */
export const FONT_SIZE_OPTIONS: IOption[] = [
    { value: 8, label: '8' },
    { value: 9, label: '9' },
    { value: 10, label: '10' },
    { value: 11, label: '11' },
    { value: 12, label: '12' },
    { value: 14, label: '14' },
    { value: 16, label: '16' },
    { value: 18, label: '18' },
    { value: 20, label: '20' },
    { value: 24, label: '24' },
    { value: 26, label: '26' },
    { value: 28, label: '28' },
    { value: 36, label: '36' },
    { value: 48, label: '48' },
    { value: 72, label: '72' },
];

/**
 * Maximum image size allowed for upload, defined in bytes (200 KB).
 */
export const MAX_SIZE = 204800;
