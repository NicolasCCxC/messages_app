import { login } from './actions';
import fetchClient from '@api/FetchClient';
import LocalStorage from '@utils/LocalStorage';
import { Login } from '@constants/Login';
import { urls } from '@api/Urls';

jest.mock('@utils/LocalStorage');
jest.mock('@api/FetchClient');

const mockedFetchClient = fetchClient as jest.Mocked<typeof fetchClient>;
const mockedLocalStorage = LocalStorage as jest.Mocked<typeof LocalStorage>;

describe('auth/actions -> login thunk', () => {
    const mockDispatch = jest.fn();
    const mockGetState = jest.fn();

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('resuelve "fulfilled", llama a fetchClient.post y guarda el token en caso de éxito', async () => {
        const mockCredentials = { email: 'test@test.com', password: 'password' };
        const mockApiResponse = { data: { user: { name: 'Test User' }, accessToken: 'fake-token-123' } };
        mockedFetchClient.post.mockResolvedValue(mockApiResponse);

        const thunkAction = login(mockCredentials);
        const result = await thunkAction(mockDispatch, mockGetState, undefined);

        expect(mockedFetchClient.post).toHaveBeenCalledWith(urls.auth.login, mockCredentials);
        expect(mockedLocalStorage.set).toHaveBeenCalledWith(Login.UserToken, 'fake-token-123');
        
        expect(result.type).toBe('auth/login/fulfilled');
        expect(result.payload).toEqual(mockApiResponse.data);
    });

    it('resuelve "rejected" si la respuesta de la API no contiene data.user', async () => {
        const mockCredentials = { email: 'test@test.com', password: 'password' };
        const mockApiResponse = { message: 'Credenciales inválidas', data: {} };
        mockedFetchClient.post.mockResolvedValue(mockApiResponse);

        const thunkAction = login(mockCredentials);
        const result = await thunkAction(mockDispatch, mockGetState, undefined);

        expect(result.type).toBe('auth/login/rejected');
        expect(result.payload).toBe('Credenciales inválidas');
        expect(mockedLocalStorage.set).not.toHaveBeenCalled();
    });

    it('resuelve "rejected" si fetchClient.post lanza un error de red', async () => {
        const mockCredentials = { email: 'test@test.com', password: 'password' };
        const networkError = new Error('Network Failure');
        mockedFetchClient.post.mockRejectedValue(networkError);

        const thunkAction = login(mockCredentials);
        const result = await thunkAction(mockDispatch, mockGetState, undefined);

        expect(result.type).toBe('auth/login/rejected');
        expect(result.payload).toBe(networkError);
    });
});