/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { urls } from '@api/Urls';
import { apiDeleteInput, apiGetInputs, apiPatchInput, apiPostInput } from '@api/ProductInput';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest } from '@models/Request';
import { addItem, deleteItem, replaceItem } from '@utils/Array';
import { removeEmptyStrings } from '@utils/Object';
import { extractErrorMessage } from '@utils/RequestError';

export const createInput = createAsyncThunk(
    'productInput/createInput',
    async (input: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.productInput.post, removeEmptyStrings(input));
            const {
                data,
                message: [message],
            }: any = await apiPostInput(request);

            const { productInput }: any = getState();
            return { data: addItem(productInput.inputs, data), message };
        } catch (error) {
            return rejectWithValue({ data: null, message: extractErrorMessage(error) });
        }
    }
);

export const deleteInput = createAsyncThunk('productInput/deleteInput', async (id: string, { getState, rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.productInput.delete(id));
        const {
            data,
            message: [message],
        }: any = await apiDeleteInput(request);

        const { productInput }: any = getState();

        return { data: deleteItem(productInput.inputs, data?.id), message };
    } catch (error) {
        return rejectWithValue({ data: null, message: extractErrorMessage(error) });
    }
});

export const getInputs = createAsyncThunk('productInput/getInputs', async (params: IGenericRecord, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.productInput.get(params));
        const { data }: any = await apiGetInputs(request);
        return data;
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const updateInput = createAsyncThunk(
    'productInput/updateInput',
    async (input: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.productInput.patch(input.id), removeEmptyStrings(input));
            const {
                data,
                message: [message],
            }: any = await apiPatchInput(request);

            const { productInput }: any = getState();
            return { data: replaceItem(productInput.inputs, data), message };
        } catch (error) {
            return rejectWithValue({ data: null, message: extractErrorMessage(error) });
        }
    }
);

export const getAllInputs = createAsyncThunk('productInput/getAllInputs', async (id: string, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.productInput.getAll(id));
        const { data }: any = await apiGetInputs(request);
        return data;
    } catch (error) {
        return rejectWithValue(String(error));
    }
});
