export { Button } from './Button';

/**
 * These are the types of the button
 */
type ButtonType = React.ButtonHTMLAttributes<HTMLButtonElement>['type'];

/**
 * Interface for the Button component props
 *
 * @typeParam text: string - The text to be displayed on the button
 * @typeParam color: 'primary' | 'secondary' - Optional determines the visual style of the button
 * @typeParam onClick: React.MouseEventHandler<HTMLButtonElement> - Optional click event handler for the button
 * @typeParam isIcon: boolean - Optional prop to indicate if the button should be rendered as an icon button
 * @typeParam buttonClassName: string - Optional prop to class styles component
 * @typeParam type: string - Optional button type
 * @typeParam disabled: boolean - Optional state to disabled button
 * @typeParam textClassName: string - Optional prop to class styles text component
 */
export interface IButtonProps {
    text: string;
    color?: 'primary' | 'secondary';
    onClick?: React.MouseEventHandler<HTMLButtonElement>;
    isIcon?: boolean;
    buttonClassName?: string;
    type?: ButtonType;
    disabled?: boolean;
    textClassName?: string;
}
