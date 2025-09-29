import { createBrowserRouter } from 'react-router-dom';
import NotFound from '@pages/NotFound';
import { IMenuItem, MENU_ITEMS } from '@constants/MenuItems';
import App from './App';

const extractRoutes = (menuItems: IMenuItem[]): { path: string; element: React.ReactNode }[] => {
    const routes: { path: string; element: React.ReactNode }[] = [];

    menuItems.forEach(item => {
        if (item.subItems) {
            item.subItems.forEach(subItem => {
                const Component = subItem.component;

                routes.push({
                    path: subItem.path,
                    element: <Component />,
                });
            });
        }
    });

    return routes;
};

const router = createBrowserRouter([
    {
        path: '/',
        element: <App />,
        errorElement: <NotFound />,
        children: [...extractRoutes(MENU_ITEMS)],
    },
]);

export default router;
