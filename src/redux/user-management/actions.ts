/* eslint-disable @typescript-eslint/no-explicit-any */
import { createAsyncThunk } from '@reduxjs/toolkit';
import { urls } from '@api/Urls';
import { FetchRequest, IParams } from '@models/Request';
import { apiGetUserManagement, apiPatchUserManagement, apiPostUserManagement } from '@api/UsersManagement';
import { IGenericRecord } from '@models/GenericRecord';
import { replaceItem } from '@utils/Array';

export const getUserManagement = createAsyncThunk('user/getUserManagement', async (params: IParams, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.userManagement.get(params));
        // eslint-disable-next-line
        const { data }: any = await apiGetUserManagement(request);
        return { ...data, content: data.content.map((user: IGenericRecord) => ({ ...user, userName: user.name })) };
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const createUserManagement = createAsyncThunk(
    'product/createProductManagement',
    async (userData: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.userManagement.post, userData);
            const { data, message }: any = await apiPostUserManagement(request);
            return { data: { ...data, userName: data.name }, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const modifyUserManagement = createAsyncThunk(
    'user/modifyUserManangement',
    async (userData: IGenericRecord, { getState, rejectWithValue }) => {
        try {
            const { id, ...userModifications } = userData;
            const request = new FetchRequest(urls.userManagement.patch(id), userModifications);
            const { data, message }: any = await apiPatchUserManagement(request);
            const { userManagement }: any = getState();
            return { data: replaceItem(userManagement.users, { ...data, userName: data.name }), message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);
