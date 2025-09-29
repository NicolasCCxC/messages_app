import { render, screen } from '@testing-library/react';
import App from './App';

jest.mock('@components/sidebar', () => ({
    Sidebar: () => <div data-testid="fake-sidebar">SB</div>,
}));

jest.mock('@pages/login', () => ({
    __esModule: true,
    default: () => <div data-testid="fake-login">Login</div>,
}));

let selectorState: any;
const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
    useAppDispatch: () => dispatchMock,
    useAppSelector: (sel: any) => sel(selectorState),
}));

jest.mock('react-router-dom', () => ({
    ...jest.requireActual('react-router-dom'),
    useNavigate: () => jest.fn(),
    useLocation: jest.fn(),
}));

import { useLocation } from 'react-router-dom';

describe('App', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        selectorState = {
            auth: {
                token: 'teadsadasd',
                user: {
                    name: 'Prueba',
                    roles: [{ id: 1, description: 'testing' }],
                },
            },
        };
        (useLocation as jest.Mock).mockReturnValue({
            pathname: '/',
            search: '',
            hash: '',
            state: null,
        });
    });

    it('Validar renderizado con token', () => {
        render(<App />);
        expect(screen.queryByText('Bienvenido')).toBeInTheDocument();
    });

    it('Validar renderizado sin token', () => {
        selectorState.auth.token = null;
        render(<App />);
        expect(screen.queryByText('Login')).toBeInTheDocument();
    });
});
