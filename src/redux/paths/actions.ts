/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { urls } from '@api/Urls';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest } from '@models/Request';
import { apiDeletePath, apiGetPaths, apiPatchPath, apiPostPath } from '@api/ExitPaths';
import { addItem, deleteItem, replaceItem } from '@utils/Array';

export const getExitPaths = createAsyncThunk('paths/getExitPaths', async (params: IGenericRecord, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.exitPaths.get(params));
        const { data }: any = await apiGetPaths(request);
        return data;
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const createPath = createAsyncThunk('paths/createPath', async (path: IGenericRecord, { getState, rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.exitPaths.post, path);
        const {
            data,
            message: [message],
        }: any = await apiPostPath(request);
        const { paths }: any = getState();
        return { data: addItem(paths.paths, data), message };
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const deletePath = createAsyncThunk('paths/deletePath', async (id: string, { getState, rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.exitPaths.delete(id));
        const {
            data,
            message: [message],
        }: any = await apiDeletePath(request);
        
        const { paths }: any = getState();

        return { data: deleteItem(paths.paths, data?.id), message };
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const updatePath = createAsyncThunk('paths/updatePath', async (path: IGenericRecord, { getState, rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.exitPaths.patch(path.id), path);
        const {
            data,
            message: [message],
        }: any = await apiPatchPath(request);

        const { paths }: any = getState();

        return { data: replaceItem(paths.paths, data), message };
    } catch (error) {
        return rejectWithValue(String(error));
    }
});
