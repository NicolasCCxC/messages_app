/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest } from '@models/Request';
import { urls } from '@api/Urls';
import {
    apiDeleManageContentProduct,
    apiGetManageContentProduct,
    apiPatchManageContentProduct,
    apiPostManageContentProduct,
} from '@api/ManageContentProduct';
import { deleteItem } from '@utils/Array';
import { MAX_TABLE_ITEMS, MIN_TABLE_ITEMS } from '@constants/MaxAndMinValues';
import { getRequiredFields } from '@utils/GetRequiredFields';

export const getManageContentProduct = createAsyncThunk(
    'manageContentProduct/getManageContentProduct',
    async (params: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.manageContentProduct.get(params));

            const { data }: any = await apiGetManageContentProduct(request);
            const content = data.content.map((item: IGenericRecord) => ({
                ...item,
                product: item.product.id,
                mappedRequiredFields: getRequiredFields(item.requiredFields),
            }));

            return { ...data, content };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const createManageContentProduct = createAsyncThunk(
    'manageContentProduct/createManageContentProduct',
    async (manageData: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.manageContentProduct.post, manageData);
            const { data, message }: any = await apiPostManageContentProduct(request);
            const { manageContentProduct }: any = getState();
            const content = [...manageContentProduct.content];
            return {
                content: [
                    { ...data, product: data.product.id, mappedRequiredFields: getRequiredFields(data.requiredFields) },
                    ...content.slice(MIN_TABLE_ITEMS, MAX_TABLE_ITEMS),
                ],
                message,
            };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const modifyManageContentProduct = createAsyncThunk(
    'manageContentProduct/modifyManageContentProduct',
    async ({ formData, id }: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.manageContentProduct.patch(id), formData);
            const { data, message }: any = await apiPatchManageContentProduct(request);
            const { manageContentProduct }: any = getState();
            const content = manageContentProduct.content.map((product: IGenericRecord) =>
                product.id === data.id
                    ? { ...data, product: data.product.id, mappedRequiredFields: getRequiredFields(data.requiredFields) }
                    : product
            );
            return { content, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const deleteContentProduct = createAsyncThunk(
    'productInput/deleteContentProduct',
    async (id: string, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.manageContentProduct.delete(id));
            const {
                message: [message],
            }: any = await apiDeleManageContentProduct(request);

            const { manageContentProduct }: any = getState();

            return { data: deleteItem(manageContentProduct.content, id), message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);
