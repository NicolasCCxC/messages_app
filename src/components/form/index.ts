import { ReactNode } from 'react';

export { Form } from './Form';

/**
 * This interface describes the form props
 *
 * @typeParam children: ReactNode - Form children
 * @typeParam sendWithEnter: boolean - Optional prop indicating whether to submit the form with enter
 * @typeParam className: string - Optional prop indicating component classes
 */
export interface IFormProps {
    children: ReactNode;
    sendWithEnter?: boolean;
    className?: string;
}

/**
 * This is used to detect if the key pressed is enter
 */
export const ENTER = 'Enter';
