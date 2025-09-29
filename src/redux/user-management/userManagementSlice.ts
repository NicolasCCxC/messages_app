import { createSlice } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { createUserManagement, getUserManagement, modifyUserManagement } from './actions';

interface IUserManagementState {
    data: IGenericRecord;
    users: IGenericRecord[];
    message: string;
    error: string | null;
    status: 'idle' | 'succeeded' | 'failed';
}

const initialState: IUserManagementState = {
    data: {},
    users: [],
    message: '',
    error: null,
    status: 'idle',
};

const userManagementSlice = createSlice({
    name: 'userManagement',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder
            .addCase(getUserManagement.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.data = action.payload;
                state.users = action.payload.content;
                state.error = null;
            })
            .addCase(createUserManagement.fulfilled, (state, action) => {
                state.status = 'succeeded';
                if (state?.users?.length >= 10) state?.users?.pop();
                state.data = { ...state.data, content: [action.payload.data, ...state.users] };
                state.users = [action.payload.data, ...state.users];
                state.message = action.payload.message;
                state.error = null;
            })
            .addCase(modifyUserManagement.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.users = action.payload.data;
                state.message = action.payload.message;
            })
            .addCase(getUserManagement.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.payload as string;
            })
            .addCase(createUserManagement.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.payload as string;
            });
    },
});

export default userManagementSlice.reducer;
