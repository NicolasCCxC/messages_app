/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { apiGetFormats, apiPatchFormat, apiPostFormat } from '@api/Pdf';
import { urls } from '@api/Urls';
import type { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest, type IParams } from '@models/Request';
import { extractErrorMessage } from '@utils/RequestError';

export const activateFormat = createAsyncThunk('pdf/activateFormat', async (id: string, { dispatch, rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.pdf.activateFormat(id));
        const {
            data,
            message: [message],
        }: any = await apiPatchFormat(request);
        if (data) await dispatch(getFormats({ page: 0 }));
        return message;
    } catch (error) {
        return rejectWithValue(extractErrorMessage(error));
    }
});

export const createFormat = createAsyncThunk('pdf/postFormat', async (format: IGenericRecord, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.pdf.postFormat, format);
        const {
            message: [message],
        }: any = await apiPostFormat(request);
        return { error: false, message };
    } catch (error) {
        return rejectWithValue({ error: true, message: extractErrorMessage(error) });
    }
});

export const getFormats = createAsyncThunk('pdf/getFormats', async (params: IParams, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.pdf.getFormats(params));
        const { data }: any = await apiGetFormats(request);
        return data;
    } catch (error) {
        return rejectWithValue(error);
    }
});

export const getProductObjects = createAsyncThunk('pdf/getProductObjects', async (id: string, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.pdf.getProductObjects(id));
        const { data }: any = await apiGetFormats(request);
        return data;
    } catch (error) {
        return rejectWithValue(error);
    }
});

export const updateFormat = createAsyncThunk(
    'pdf/updateFormat',
    async ({ id, ...restFormat }: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.pdf.updateFormat(id), restFormat);
            const {
                message: [message],
            }: any = await apiPostFormat(request);
            return { error: false, message };
        } catch (error) {
            return rejectWithValue({ error: true, message: extractErrorMessage(error) });
        }
    }
);
