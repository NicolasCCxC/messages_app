export { Title } from './Title';

/**
 * Interface for the Title component props
 *
 * @typeParam title: string - The text content to be displayed as the title
 * @typeParam className: string - Optional prop to apply custom CSS classes to the title element
 */
export interface ITitleProps {
    title: string;
    className?: string;
}
