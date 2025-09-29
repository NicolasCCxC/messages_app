/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest, IParams } from '@models/Request';
import { apiGetIndex, apiPostIndex } from '@api/ExecutingIndexGeneration';
import { urls } from '@api/Urls';

export const createIndex = createAsyncThunk(
    'executingIndexGeneration/createIndex',
    async (objectData: IGenericRecord, { rejectWithValue, getState }) => {
        try {
            const request = new FetchRequest(urls.executingIndexGeneration.post, objectData);
            const { data, message }: any = await apiPostIndex(request);

            const { executingIndexGeneration }: any = getState();
            let elements = [...executingIndexGeneration.elements];
            if (elements.length >= 10) elements?.pop();
            elements = [data, ...elements];
            return { elements, message };
        } catch (error) {
            console.log(error);
            return rejectWithValue(String(error));
        }
    }
);

export const getIndex = createAsyncThunk('executingIndexGeneration/getIndex', async (params: IParams, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.executingIndexGeneration.get(params));
        const { data }: any = await apiGetIndex(request);
        return data;
    } catch (error) {
        return rejectWithValue(String(error));
    }
});
