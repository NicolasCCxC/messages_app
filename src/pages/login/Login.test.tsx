import { render, screen, fireEvent, waitFor } from '@testing-library/react';

jest.mock('./Login.scss', () => ({}), { virtual: true });

jest.mock('@components/button', () => ({
  __esModule: true,
  Button: ({ text, onClick, type = 'button' }: any) =>
    <button type={type} onClick={onClick}>{text}</button>,
}));
jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ placeholder, value, onChange, name, type = 'text' }: any) =>
    <input placeholder={placeholder} value={value} onChange={onChange} name={name} type={type} />,
}));

jest.mock('@utils/Input', () => ({ __esModule: true, validatePattern: () => true }));

const loginMock = jest.fn((payload: any) => ({ type: 'auth/login', payload }));
jest.mock('@redux/auth/actions', () => ({ __esModule: true, login: (p: any) => loginMock(p) }));

jest.mock('@redux/auth/authSlice', () => ({ __esModule: true, cleanError: () => ({ type: 'auth/cleanError' }) }));

const dispatchMock = jest.fn(async (a) => a);
let mockedState = { auth: { error: '' } };
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (selector: any) => selector(mockedState),
}));

import Login from './Login';

describe('Login page', () => {
  beforeEach(() => {
    dispatchMock.mockClear();
    loginMock.mockClear();
    mockedState = { auth: { error: '' } };
  });

  it('renderiza campos y botones', () => {
    render(<Login />);
    expect(screen.getByPlaceholderText('Usuario')).toBeInTheDocument();
    expect(screen.getByPlaceholderText('Contraseña')).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /Cancelar/i })).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /Iniciar sesión/i })).toBeInTheDocument();
  });

  it('envía login con credenciales válidas', async () => {
    render(<Login />);
    fireEvent.change(screen.getByPlaceholderText('Usuario'), { target: { name: 'email', value: 'testuser' } });
    fireEvent.change(screen.getByPlaceholderText('Contraseña'), { target: { name: 'password', value: 'pass123' } });
    fireEvent.click(screen.getByRole('button', { name: /Iniciar sesión/i }));
    await waitFor(() => expect(loginMock).toHaveBeenCalledWith({ email: 'testuser', password: 'pass123' }));
    expect(dispatchMock).toHaveBeenCalled();
  });

  it('no envía login si faltan datos', async () => {
    render(<Login />);
    fireEvent.click(screen.getByRole('button', { name: /Iniciar sesión/i }));
    await waitFor(() => expect(loginMock).not.toHaveBeenCalled());
  });

  it('muestra error de redux', () => {
    mockedState.auth.error = 'Usuario o contraseña inválidos';
    render(<Login />);
    expect(screen.getByText('Usuario o contraseña inválidos')).toBeInTheDocument();
  });

  it('Cancelar llama window.close', () => {
    const spy = jest.spyOn(window, 'close').mockImplementation(() => {});
    render(<Login />);
    fireEvent.click(screen.getByRole('button', { name: /Cancelar/i }));
    expect(spy).toHaveBeenCalled();
    spy.mockRestore();
  });
});
