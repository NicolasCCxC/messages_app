import { createSlice } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { createManageContentProduct, deleteContentProduct, getManageContentProduct, modifyManageContentProduct } from './actions';

interface IManageContentProduct {
    manageData: IGenericRecord;
    content: IGenericRecord[];
    message: string;
}

const initialState: IManageContentProduct = {
    manageData: {},
    content: [],
    message: '',
};

const manageContentProductSlice = createSlice({
    name: 'manageContentProduct',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder
            .addCase(getManageContentProduct.fulfilled, (state, { payload }) => {
                state.manageData = payload;
                state.content = payload.content;
            })
            .addCase(createManageContentProduct.fulfilled, (state, { payload }) => {
                state.content = payload.content;
            })
            .addCase(modifyManageContentProduct.fulfilled, (state, { payload }) => {
                state.content = payload.content;
            })
            .addCase(deleteContentProduct.fulfilled, (state, { payload }) => {
                state.content = payload.data;
            });
    },
});

export default manageContentProductSlice.reducer;
