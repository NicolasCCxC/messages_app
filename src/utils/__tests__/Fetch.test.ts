import FetchInstance from '../Fetch';
import localStorage from '../LocalStorage';
import { Login } from '@constants/Login';
import { UNAUTHORIZED, FORBIDDEN } from '@constants/StatusCodes';

jest.mock('../LocalStorage', () => ({
  get: jest.fn(),
  clearKey: jest.fn(),
}));

global.fetch = jest.fn();

describe('Fetch utility', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('debería realizar una petición fetch sin el token de autorización si no existe', async () => {
    (localStorage.get as jest.Mock).mockReturnValue(null);
    
    (global.fetch as jest.Mock).mockResolvedValue({
      ok: true,
      json: () => Promise.resolve({ data: 'success' }),
    });

    await FetchInstance.request('test-url', { method: 'GET' });

    expect(global.fetch).toHaveBeenCalledWith('test-url', {
      method: 'GET',
      headers: {},
    });
    expect(localStorage.get).toHaveBeenCalledWith(Login.UserToken);
  });

  it('debería añadir el token de autorización a los headers si existe', async () => {
    const fakeToken = 'my-secret-token';
    (localStorage.get as jest.Mock).mockReturnValue(fakeToken);

    (global.fetch as jest.Mock).mockResolvedValue({
      ok: true,
      json: () => Promise.resolve({ data: 'success' }),
    });

    await FetchInstance.request('test-url', { method: 'GET' });

    expect(global.fetch).toHaveBeenCalledWith('test-url', {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${fakeToken}`,
      },
    });
  });

  it('debería lanzar un error si la respuesta de la petición no es "ok"', async () => {
    const errorResponse = { message: 'Something went wrong' };
    (global.fetch as jest.Mock).mockResolvedValue({
      ok: false,
      json: () => Promise.resolve(errorResponse),
    });

    await expect(FetchInstance.request('test-url', {})).rejects.toThrow(
      JSON.stringify(errorResponse)
    );
  });

  it('debería limpiar localStorage si el error es UNAUTHORIZED (401)', async () => {
    const error = {
      response: { status: UNAUTHORIZED },
    };
    (global.fetch as jest.Mock).mockRejectedValue(error);

    await expect(FetchInstance.request('test-url', {})).rejects.toBe(error);

    expect(localStorage.clearKey).toHaveBeenCalledWith(Login.UserToken);
    expect(localStorage.clearKey).toHaveBeenCalledWith(Login.UserData);
  });

  it('debería limpiar localStorage si el error es FORBIDDEN (403)', async () => {
    const error = {
      response: { status: FORBIDDEN },
    };
    (global.fetch as jest.Mock).mockRejectedValue(error);

    await expect(FetchInstance.request('test-url', {})).rejects.toBe(error);

    expect(localStorage.clearKey).toHaveBeenCalledWith(Login.UserToken);
    expect(localStorage.clearKey).toHaveBeenCalledWith(Login.UserData);
  });

  it('no debería limpiar localStorage para otros errores de red', async () => {
    const error = new Error('Network Error');
    (global.fetch as jest.Mock).mockRejectedValue(error);

    await expect(FetchInstance.request('test-url', {})).rejects.toThrow('Network Error');

    expect(localStorage.clearKey).not.toHaveBeenCalled();
  });
});