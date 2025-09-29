/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { urls } from '@api/Urls';
import { IGenericRecord } from '@models/GenericRecord';
import { FetchRequest } from '@models/Request';
import { apiGetPathsDataFiles, apiPatchPathsDataFiles, apiPostPathsDataFiles } from '@api/PathsDataFiles';
import { MAX_TABLE_ITEMS, MIN_TABLE_ITEMS } from '@constants/MaxAndMinValues';

export const getPathsDataFile = createAsyncThunk(
    'pathsDataFile/getPathsDataFile',
    async (params: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.pathsDataFiles.get(params));

            const { data }: any = await apiGetPathsDataFiles(request);

            const content = data.content.map((item: IGenericRecord) => ({ ...item, product: item.product.id }));

            return { ...data, content };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const createPathDataFile = createAsyncThunk(
    'pathsDataFile/createPathDataFile',
    async (pathDataFile: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.pathsDataFiles.post, pathDataFile);
            const { data: newData, message }: any = await apiPostPathsDataFiles(request);
            const { pathsDataFiles }: any = getState();

            const currentPaths = [...pathsDataFiles.paths];

            const updatedPaths = currentPaths.map(item => (updateSimils(item, newData) ? { ...item, active: false } : item));

            const finalPaths = [{ ...newData, product: newData.product.id }, ...updatedPaths].slice(
                MIN_TABLE_ITEMS,
                MAX_TABLE_ITEMS
            );

            return {
                content: finalPaths,
                message,
            };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const modifyPathDataFile = createAsyncThunk(
    'product/modifyPathDataFile',
    async (productData: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.pathsDataFiles.patch(productData.id), productData);
            const { data: newData, message }: any = await apiPatchPathsDataFiles(request);
            const { pathsDataFiles }: any = getState();
            const currentPaths = [...pathsDataFiles.paths];

            const finalPaths = currentPaths
                .map(item => (updateSimils(item, newData) ? { ...item, active: false } : item))
                .map(item => (item.id === newData.id ? { ...newData, product: newData.product.id } : item));

            return { content: finalPaths, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

const updateSimils = ({ id, active, product }: IGenericRecord, newItem: IGenericRecord): boolean =>
    newItem.active && id !== newItem.id && product === newItem.product.id && active;
