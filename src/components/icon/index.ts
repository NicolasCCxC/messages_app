export { Icon } from './Icon';

/**
 * Interface for the Icon component props
 *
 * @typeParam name: IconsNames - The name of the icon to be displayed
 * @typeParam alt: string - Optional alternative text for the icon image
 * @typeParam className: string - Optional CSS class name for custom styling
 * @typeParam onClick: React.MouseEventHandler<HTMLImageElement> - Optional click event handler for the icon
 * @typeParam onKeyDown: React.KeyboardEventHandler<HTMLImageElement> - Optional keydown event handler for keyboard accessibility
 * @typeParam IconName - Optional icon name displayed on hover
 */
export interface IIconProps {
    name: IconName;
    alt?: string;
    className?: string;
    onClick?: React.MouseEventHandler<HTMLImageElement>;
    onKeyDown?: React.KeyboardEventHandler<HTMLImageElement>;
    hoverIcon?: IconName;
}

/**
 * Type definition for the names of available icons
 */
export type IconName =
    | 'activateBlue'
    | 'activateGray'
    | 'activateRed'
    | 'activateLocked'
    | 'arrowBack'
    | 'arrowDown'
    | 'arrowLeftBlueOutline'
    | 'arrowLeftBlueRounded'
    | 'arrowRightBlueOutline'
    | 'cancelRed'
    | 'csv'
    | 'cancelWhite'
    | 'checkBlue'
    | 'checkCircle'
    | 'checkRed'
    | 'circleClose'
    | 'contactPage'
    | 'dragIndicator'
    | 'exclamationRed'
    | 'exclamationWhite'
    | 'eyeBlue'
    | 'eyeGray'
    | 'eyeRed'
    | 'home'
    | 'image'
    | 'insertChart'
    | 'lockPerson'
    | 'logoAvVillas'
    | 'mergeCell'
    | 'pencilBlue'
    | 'pencilGray'
    | 'pencilRed'
    | 'plusWhite'
    | 'pdf'
    | 'roundedBottomLeft'
    | 'roundedBottomRight'
    | 'roundedTopLeft'
    | 'roundedTopRight'
    | 'search'
    | 'settings'
    | 'shape'
    | 'table'
    | 'text'
    | 'trashBlue'
    | 'trashGray'
    | 'trashRed'
    | 'upload';
