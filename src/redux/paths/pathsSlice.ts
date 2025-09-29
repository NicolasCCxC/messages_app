import { createSlice } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { createPath, deletePath, getExitPaths, updatePath } from './actions';

interface IState {
    paths: IGenericRecord[];
    pages: number;
}

const initialState: IState = {
    paths: [],
    pages: 1,
};

const userRolesSlice = createSlice({
    name: 'userRoles',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder.addCase(getExitPaths.fulfilled, (state, { payload }) => {
            state.paths = payload.content;
            state.pages = payload.totalPages;
        });
        builder.addCase(createPath.fulfilled, (state, { payload }) => {
            state.paths = payload.data;
        });
        builder.addCase(updatePath.fulfilled, (state, { payload }) => {
            state.paths = payload.data;
        });
        builder.addCase(deletePath.fulfilled, (state, { payload }) => {
            state.paths = payload.data;
        });
    },
});

export default userRolesSlice.reducer;
