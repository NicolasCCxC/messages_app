import { createSlice } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { createPathDataFile, getPathsDataFile, modifyPathDataFile } from './actions';

interface IPathsDataFilesState {
    data: IGenericRecord;
    paths: IGenericRecord[];
    error: string | null;
    message: string;
}

const initialState: IPathsDataFilesState = {
    data: {},
    paths: [],
    error: null,
    message: '',
};

const pathsDataFileSlice = createSlice({
    name: 'pathsDataFile',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder
            .addCase(getPathsDataFile.fulfilled, (state, { payload }) => {
                state.data = payload;
                state.paths = payload.content;
            })
            .addCase(createPathDataFile.fulfilled, (state, { payload }) => {
                state.paths = payload.content;
            })
            .addCase(modifyPathDataFile.fulfilled, (state, { payload }) => {
                state.paths = payload.content;
            });
    },
});

export default pathsDataFileSlice.reducer;
