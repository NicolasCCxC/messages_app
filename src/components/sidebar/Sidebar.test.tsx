import { render, screen, fireEvent } from '@testing-library/react';
import { Sidebar } from './Sidebar';

jest.mock('@components/icon', () => ({
    Icon: (props: any) => <span data-testid="icon" {...props} />,
}));

let selectorState: any;
const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
    useAppDispatch: () => dispatchMock,
    useAppSelector: (sel: any) => sel(selectorState),
}));

jest.mock('react-router-dom', () => ({
    ...jest.requireActual('react-router-dom'),
    useLocation: jest.fn(),
    useNavigate: () => jest.fn(),
    Link: ({ children, to }: any) => <a href={to}>{children}</a>,
}));

import { useLocation } from 'react-router-dom';

describe('Sidebar', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        selectorState = {
            sidebar: {
                isOpen: false,
            },
        };
        (useLocation as jest.Mock).mockReturnValue({
            pathname: '/product-management',
            search: '',
            hash: '',
            state: null,
        });
    });
    it('muestra el submenu al hacer clic en una sesión', () => {
        render(<Sidebar />);

        expect(screen.queryByText('Parámetros seguridad')).toBeInTheDocument();

        fireEvent.click(screen.queryByText('Parámetros seguridad') as Element);
        expect(screen.queryByText('Gestión de roles de usuario')).toBeInTheDocument();

        fireEvent.click(screen.queryByText('Parámetros seguridad') as Element);
        expect(screen.queryByText('Gestión de roles de usuario')).not.toBeInTheDocument();
    });

    it('Navegación Link', () => {
        render(<Sidebar />);

        expect(screen.queryByText('Parámetros seguridad')).toBeInTheDocument();

        fireEvent.click(screen.queryByText('Parámetros seguridad') as Element);
        const link = screen.queryByText('Gestión de roles de usuario');
        expect(link?.closest('a')).toHaveAttribute('href', '/user-roles');
    });

    it('Abre automaticamente la sesión activa si la ruta coincide con el useLocation', () => {
        render(<Sidebar />);
        expect(screen.queryByText('Gestión de productos')).toBeInTheDocument();
    });
});
