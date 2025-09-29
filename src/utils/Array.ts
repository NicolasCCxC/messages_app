import type { IGenericRecord } from '@models/GenericRecord';

/**
 * This replaces the sent element in an array
 *
 * @param currentData: IGenericRecord[] - Current data
 * @param newItem: IGenericRecord - New element to replace in the array
 * @returns IGenericRecord[]
 */
export const replaceItem = (currentData: IGenericRecord[], newItem: IGenericRecord): IGenericRecord[] => {
    return currentData.map(item => (item?.id === newItem?.id ? newItem : item));
};

/**
 * This adds a new element to an array
 *
 * @param currentData: IGenericRecord[] - Current data
 * @param newItem: IGenericRecord - New element to add to the array
 * @returns IGenericRecord[]
 */
export const addItem = (currentData: IGenericRecord[], newItem: IGenericRecord): IGenericRecord[] => {
    return newItem.id ? [...currentData, newItem] : currentData;
};

/**
 * This removes an element from the array
 *
 * @param currentData: IGenericRecord[] - Current data
 * @param id: string - Id of the element to be deleted
 * @returns IGenericRecord[]
 */
export const deleteItem = (currentData: IGenericRecord[], id: string): IGenericRecord[] => {
    return currentData.filter(item => item?.id !== id);
};

/**
 * Validates if a single value is considered empty
 *
 * @param value: string | boolean | object | number - Value to validate
 * @returns boolean
 */
export const isEmptyValue = (value: string | boolean | object | number): boolean => {
    if (typeof value === 'string') return !value.trim().length;
    if (value === undefined || value === null) return true;
    if (typeof value === 'object') return Object.keys(value).length === 0;
    return false;
};

/**
 * Validates if any value in an object is empty
 *
 * @param formValues: IGenericRecord - Object with values to validate
 * @returns boolean
 */
export const hasEmptyFields = (formValues: IGenericRecord): boolean => {
    return Object.values(formValues).some(isEmptyValue);
};

/**
 * This filters the data based on the submitted property and search value.
 *
 * @param data: IGenericRecord[] - Data to filter
 * @param { key, value }: { key: string; value: string } - Key and search value
 * @returns IGenericRecord[]
 */
export const filterData = (data: IGenericRecord[], { key, value }: { key: string; value: string }): IGenericRecord[] => {
    return data.filter(item => item[key].toLowerCase().includes(value.toLowerCase()));
};
