import { IconName } from '@components/icon';
import ExitPaths from '@pages/exit-paths';
import ManageContentProduct from '@pages/manage-content-product';
import PathsDataFiles from '@pages/paths-data-files';
import ProductManagement from '@pages/product-management';
import UserManagement from '@pages/user-management';
import UserRoles from '@pages/user-roles';
import ProductInput from '@pages/product-input';
import PdfPresentation from '@pages/pdf-presentation';
import ObjectManageFormat from '@pages/object-manage-format';
import InputFileUpload from '@pages/input-file-upload';
import ExecutionAssistedProcess from '@pages/execution-assisted-process';
import AuditConsultation from '@pages/audit-consultation';
import QueryingHistoricalProcesses from '@pages/querying-historical-processes';
import ExecutingIndexGeneration from '@pages/executing-index-generation';

/**
 * Interface for the SubItem object
 *
 * @typeParam title: string - The display text for the sub-item
 * @typeParam path: string - The URL path associated with the sub-item
 * @typeParam component: React.FC - The React functional component to be rendered for this sub-item
 */
export interface ISubItem {
    title: string;
    path: string;
    component: React.FC;
}

/**
 * Interface for the MenuItem object
 *
 * @typeParam title: string - The display text for the menu item
 * @typeParam icon: IconName - The name of the icon to be displayed alongside the menu item
 * @typeParam subItems: ISubItem[] - An array of sub-items associated with this menu item
 */
export interface IMenuItem {
    title: string;
    icon: IconName;
    subItems: ISubItem[];
}

/**
 * This array contains all routes an tittles of routes in SideBar component
 */
export const MENU_ITEMS: IMenuItem[] = [
    {
        title: 'Parámetros seguridad',
        icon: 'lockPerson',
        subItems: [
            { title: 'Gestión de roles de usuario', path: '/user-roles', component: UserRoles },
            { title: 'Gestión de usuarios', path: '/user-management', component: UserManagement },
        ],
    },
    {
        title: 'Parámetros generales',
        icon: 'settings',
        subItems: [
            { title: 'Gestión de productos', path: '/product-management', component: ProductManagement },
            { title: 'Rutas para archivo de datos', path: '/paths-data-files', component: PathsDataFiles },
            {
                title: 'Rutas para extractos y archivo de índices',
                path: '/exit-paths',
                component: ExitPaths,
            },
            {
                title: 'Gestión del contenido del archivo de índice por producto',
                path: '/manage-content-product',
                component: ManageContentProduct,
            },
            {
                title: 'Definición de estructura de entrada por producto',
                path: '/product-input',
                component: ProductInput,
            },
            {
                title: 'Gestión de objetos del formato por tipo de producto',
                path: '/object-manage-format',
                component: ObjectManageFormat,
            },
            {
                title: 'Presentación en modo diseño del Formato PDF',
                path: '/pdf-presentation',
                component: PdfPresentation,
            },
        ],
    },
    {
        title: 'Generación de extractos',
        icon: 'contactPage',
        subItems: [
            { title: 'Cargue de archivo de entrada', path: '/input-file-upload', component: InputFileUpload },
            { title: 'Ejecución del Proceso de Generación de Archivo de Índices', path: '/executing-index-generation', component: ExecutingIndexGeneration },
            {
                title: 'Ejecución del proceso de generación de extractos asistido',
                path: '/execution-assisted-process',
                component: ExecutionAssistedProcess,
            },
        ],
    },
    {
        title: 'Reportes',
        icon: 'insertChart',
        subItems: [
            { title: 'Consulta de auditoría', path: '/audit-consultation', component: AuditConsultation },
            {
                title: 'Consulta de históricos de proceso de extractos',
                path: '/querying-historical-processes',
                component: QueryingHistoricalProcesses,
            },
        ],
    },
];
