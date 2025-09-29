/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { urls } from '@api/Urls';
import { apiGetUserRoles, apiUpdateRole } from '@api/UserRoles';
import { FetchRequest } from '@models/Request';
import { IGenericRecord } from '@models/GenericRecord';
import { replaceItem } from '@utils/Array';

export const getUserRoles = createAsyncThunk('roles/getRoles', async (params: IGenericRecord, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.userRoles.get(params));
        const { data }: any = await apiGetUserRoles(request);
        return data;
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const updateRole = createAsyncThunk('roles/updateRole', async (role: IGenericRecord, { getState, rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.userRoles.update(role.id), role);
        const {
            data,
            message: [message],
        }: any = await apiUpdateRole(request);
        const { roles }: any = getState();
        return { data: replaceItem(roles.allData, data), message };
    } catch (error) {
        return rejectWithValue({ data: error, message: String(error) });
    }
});
