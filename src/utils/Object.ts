import type { IGenericRecord } from '@models/GenericRecord';

/**
 * This validates if there are empty fields in the object
 *
 * @param source: IGenericRecord - Data to validate
 * @param fields: string[] - Array with the names of the fields to be validated. If not sent, all the object properties will be validated
 * @returns boolean
 */
export const hasEmptyFields = (source: IGenericRecord, fields?: string[]): boolean => {
    const keysToCheck = fields && !!fields.length ? fields : Object.keys(source);
    return keysToCheck.some(key => !source[key]);
};

/**
 * This removes properties whose value is an empty string
 *
 * @param source: IGenericRecord - This is the object from which the properties with empty strings will be removed
 * @returns IGenericRecord
 */
export const removeEmptyStrings = (source: IGenericRecord): IGenericRecord => {
    return Object.fromEntries(Object.entries(source).filter(([, value]) => value !== ''));
};

/**
 * This validates if an object is empty
 *
 * @param data: IGenericRecord - Data to be validated
 * @returns boolean
 */
export const isEmptyObject = (data: IGenericRecord): boolean => !Object.keys(data).length;

/**
 * This removes the properties sent as an argument and returns a new object without these
 *
 * @param source: IGenericRecord - The original object to create a copy from, excluding specified properties
 * @param source: An array of property names to be removed from the resulting object
 * @returns IGenericRecord
 */
export const removeProperties = (source: IGenericRecord, keys: string[]): IGenericRecord => {
    const clone = { ...source };
    for (const key of keys) delete clone[key];
    return clone;
};
