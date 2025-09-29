/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest, IParams } from '@models/Request';
import { apiGetFile, apiPostFile } from '@api/inputFileUpload';
import { urls } from '@api/Urls';

export const createFile = createAsyncThunk(
    'inputFileUpload/createFile',
    async (objectData: IGenericRecord, { rejectWithValue, getState }) => {
        try {
            const request = new FetchRequest(urls.inputFileUpload.post, objectData);
            const { data, message }: any = await apiPostFile(request);

            const { inputFileUpload }: any = getState();
            let elements = [...inputFileUpload.elements];
            if (elements.length >= 10) elements?.pop();
            elements = [{ ...data, productName: data?.product?.description, userName: data.user.name }, ...elements];
            return { elements, message };
        } catch (error) {
            console.log(error);
            return rejectWithValue(String(error));
        }
    }
);

export const getFile = createAsyncThunk('inputFileUpload/getFile', async (params: IParams, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.inputFileUpload.get(params));
        const { data }: any = await apiGetFile(request);
        const content =
            data?.content?.map((element: IGenericRecord) => ({
                ...element,
                productName: element?.product?.description,
                userName: element.user.name,
            })) || [];
        return { data, content };
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const cancelFile = createAsyncThunk('inputFileUpload/cancelFile', async (id: string, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.inputFileUpload.cancelPost(id));
        const { data, message }: any = await apiPostFile(request);
        return { data, message };
    } catch (error) {
        console.log(error);
        return rejectWithValue(String(error));
    }
});
