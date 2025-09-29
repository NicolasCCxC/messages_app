import { createSlice } from '@reduxjs/toolkit';
import type { IGenericRecord } from '@models/GenericRecord';
import { getFormats, getProductObjects } from './actions';

interface IState {
    formats: IGenericRecord[];
    objects: IGenericRecord[];
    pages: number;
}

const initialState: IState = {
    formats: [],
    objects: [],
    pages: 1,
};

const pdfSlice = createSlice({
    name: 'pdf',
    initialState,
    reducers: {
        resetObjects: state => {
            state.objects = [];
        },
    },
    extraReducers: builder => {
        builder.addCase(getFormats.fulfilled, (state, { payload }) => {
            state.formats = payload.content;
            state.pages = payload.totalPages;
        });
        builder.addCase(getProductObjects.fulfilled, (state, { payload }) => {
            state.objects = payload;
        });
    },
});
export const { resetObjects } = pdfSlice.actions;
export default pdfSlice.reducer;
