/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest } from '@models/Request';
import { urls } from '@api/Urls';
import {
    apiDeleteObjectManageFormat,
    apiGetObjectManageFormat,
    apiPatchObjectManageFormat,
    apiPostObjectManageFormat,
} from '@api/ObjectManageFormat';
import { deleteItem } from '@utils/Array';
import { extractErrorMessage } from '@utils/RequestError';

export const getObjectManageFormat = createAsyncThunk(
    'objectManageFormat/getObjectManageFormat',
    async (params: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.objectManageFormat.get(params));

            const { data }: any = await apiGetObjectManageFormat(request);
            const elements = data.content.map((item: IGenericRecord) => ({
                ...item,
                product: item?.product?.id,
                objectName: item?.name,
            }));

            return { data, elements };
        } catch (error) {
            console.log('Error fetching object manage format:', error);
            return rejectWithValue(String(error));
        }
    }
);

export const getOneObject = createAsyncThunk('objectManageFormat/getOneObject', async (id: string, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.objectManageFormat.getOne(id));

        const { data }: any = await apiGetObjectManageFormat(request);

        return data;
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const createObjectManageFormat = createAsyncThunk(
    'objectManageFormat/createObjectManageFormat',
    async (objectData: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.objectManageFormat.post, objectData);
            const { data, message }: any = await apiPostObjectManageFormat(request);

            return { data, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const deleteObject = createAsyncThunk(
    'objectManageFormat/deleteObject',
    async (id: string, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.objectManageFormat.delete(id));

            const {
                message: [message],
            }: any = await apiDeleteObjectManageFormat(request);

            const { objectManageFormat }: any = getState();

            return { data: deleteItem(objectManageFormat.elements, id), message };
        } catch (error) {
            return rejectWithValue({ data: null, message: extractErrorMessage(error) });
        }
    }
);

export const modifyObjectManageFormat = createAsyncThunk(
    'objectManageFormat/modifyObjectManageFormat',
    async ({ diff, id }: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.objectManageFormat.patch(id), diff);
            const { data, message }: any = await apiPatchObjectManageFormat(request);
            return { data, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);
