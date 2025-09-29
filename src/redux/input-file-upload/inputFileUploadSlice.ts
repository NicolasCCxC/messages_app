import { IGenericRecord } from '@models/GenericRecord';
import { createSlice } from '@reduxjs/toolkit';
import { createFile, getFile } from './actions';

interface IState {
    data: IGenericRecord;
    elements: IGenericRecord[];
}

const initialState: IState = {
    data: {},
    elements: [],
};

const inputFileUploadSlice = createSlice({
    name: 'inputFileUpload',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder
            .addCase(createFile.fulfilled, (state, action) => {
                state.elements = action.payload.elements;
            })
            .addCase(getFile.fulfilled, (state, action) => {
                state.data = action.payload.data;
                state.elements = action.payload.content;
            });
    },
});

export default inputFileUploadSlice.reducer;
