/* eslint-disable @typescript-eslint/no-explicit-any */
import { IGenericRecord } from '@models/GenericRecord';

export default {
    get: (key: string): string => localStorage[key],
    set: (key: string, value: string): void => {
        localStorage[key] = value;
    },
    getObject: (key: string): any => (localStorage[key] ? JSON.parse(localStorage[key]) : undefined),
    setObject: (key: string, value: string | IGenericRecord): void => {
        localStorage[key] = JSON.stringify(value);
    },
    clearKey: (key: string): void => {
        localStorage.removeItem(key);
    },
};
