/* eslint-disable @typescript-eslint/no-explicit-any */
import { IGenericRecord } from '@models/GenericRecord';
import { HttpMethod } from '@models/Request';
import fetch from '@utils/Fetch';

const DEFAULT_HEADER: IGenericRecord = { 'Content-Type': 'application/json' };

class FetchClient {
    baseUrl = import.meta.env.VITE_BASE_URL;

    formatUrl(uri: string): string {
        return `${this.baseUrl}${uri}`;
    }

    sendForm(
        method: HttpMethod,
        uri: string,
        data: any,
        isFile?: boolean,
        contentType?: IGenericRecord,
        isDataForm?: boolean
    ): Promise<unknown> {
        const url = uri.includes(this.baseUrl) ? uri : this.formatUrl(uri);

        let options = {};

        options = {
            method,
            headers: contentType || {},
        };

        if (method !== HttpMethod.GET) {
            options = {
                ...options,
                body: isDataForm ? data : JSON.stringify(data),
            };
        }

        return fetch.request(url, options).then(response => {
            return isFile ? response : response.json();
        });
    }

    get(uri: string, data?: unknown, contentType = DEFAULT_HEADER, isFile = false): Promise<unknown> {
        return this.sendForm(HttpMethod.GET, uri, data, isFile, contentType);
    }

    patch(uri: string, data: unknown, isFile = false, contentType = DEFAULT_HEADER, isDataForm = false): Promise<unknown> {
        return this.sendForm(HttpMethod.PATCH, uri, data, isFile, contentType, isDataForm);
    }

    post(uri: string, data: unknown, isFile = false, contentType = DEFAULT_HEADER, isDataForm = false): Promise<unknown> {
        return this.sendForm(HttpMethod.POST, uri, data, isFile, contentType, isDataForm);
    }

    put(uri: string, data: unknown, isFile = false, contentType = DEFAULT_HEADER, isDataForm = false): Promise<unknown> {
        return this.sendForm(HttpMethod.PUT, uri, data, isFile, contentType, isDataForm);
    }

    delete(uri: string, data?: unknown, isFile = false, contentType = DEFAULT_HEADER): Promise<unknown> {
        return this.sendForm(HttpMethod.DELETE, uri, data, isFile, contentType);
    }

    getHeadersCompany(uri: string, data?: unknown, contentType = {}): Promise<unknown> {
        return this.sendForm(HttpMethod.GET, uri, data, false, contentType);
    }
}

export default new FetchClient();