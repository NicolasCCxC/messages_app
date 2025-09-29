export { Breadcrumb } from './Breadcrumb';

/**
 * Interface for the Breadcrumb component props
 *
 * @typeParam items: IBreadcrumbItem[] - Array of breadcrumb items to be displayed
 * @typeParam className: string - Optional prop to apply custom CSS classes to the breadcrumb container
 */
export interface IBreadcrumbProps {
    items: IBreadcrumbItem[];
    className?: string;
}

/**
 * Interface for the BreadcrumbItem object
 *
 * @typeParam title: string - The display text for the breadcrumb item
 * @typeParam path: string - The URL path associated with the breadcrumb item
 */
export interface IBreadcrumbItem {
    title: string;
    path: string;
}

/**
 * Array for tester breadCrumb component.
 */
export const BREADCRUMB_ITEMS = [
    { title: 'Parámetros generales', path: '#' },
    { title: 'Presentación en modo diseño del Formato PDF', path: '#' },
];
