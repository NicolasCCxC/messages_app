/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest, IParams } from '@models/Request';
import { apiGetAssistedProcess, apiPostAssistedProcess } from '@api/ExecutionAssistedProcess';
import { urls } from '@api/Urls';

export const createAssistedProcess = createAsyncThunk(
    'executionAssistedProcess/createIndex',
    async (objectData: IGenericRecord, { rejectWithValue, dispatch }) => {
        try {
            const request = new FetchRequest(urls.executingAssistedProcess.post, objectData);
            const { data, message }: any = await apiPostAssistedProcess(request);
            dispatch(getAssistedProcess({}));
            return { data, message };
        } catch (error) {
            console.log(error);
            return rejectWithValue(String(error));
        }
    }
);

export const getAssistedProcess = createAsyncThunk(
    'executionAssistedProcess/getAssistedProcess',
    async (params: IParams, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.executingAssistedProcess.get(params));
            const { data }: any = await apiGetAssistedProcess(request);
            return data;
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);
