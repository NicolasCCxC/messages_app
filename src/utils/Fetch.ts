/* eslint-disable @typescript-eslint/no-explicit-any */
import { Login } from '@constants/Login';
import { FORBIDDEN, UNAUTHORIZED } from '@constants/StatusCodes';
import localStorage from './LocalStorage';

interface IFetchInstance {
    request: (url: string, options: RequestInit) => Promise<any>;
}

const createInstance = (): IFetchInstance => {
    return {
        request: async (url: string, options: RequestInit): Promise<any> => {
            let headers = {
                ...options.headers,
            };

            const userToken = localStorage.get(Login.UserToken);
            if (userToken) {
                headers = {
                    Authorization: `Bearer ${userToken}`,
                    ...headers,
                };
            }

            const requestOptions = {
                ...options,
                headers,
            };

            try {
                const response = await fetch(url, requestOptions);

                if (!response.ok) {
                    const responser = await response.json();
                    throw new Error(JSON.stringify(responser));
                }

                return response;
            } catch (error: any) {
                if (error?.response && [UNAUTHORIZED, FORBIDDEN].includes(error?.response?.status)) {
                    localStorage.clearKey(Login.UserToken);
                    localStorage.clearKey(Login.UserData);
                }

                throw error;
            }
        },
    };
};

const instance = createInstance();

export default instance;
