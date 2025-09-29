import { createSlice } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { createInput, deleteInput, getAllInputs, getInputs, updateInput } from './actions';

interface IState {
    inputs: IGenericRecord[];
    allInputs: IGenericRecord[];
    pages: number;
}

const initialState: IState = {
    inputs: [],
    allInputs: [],
    pages: 1,
};

const productInputSlice = createSlice({
    name: 'productInput',
    initialState,
    reducers: {
        resetAllInputs: state => {
            state.allInputs = [];
        },
    },
    extraReducers: builder => {
        builder.addCase(createInput.fulfilled, (state, { payload }) => {
            state.inputs = payload.data;
        });
        builder.addCase(deleteInput.fulfilled, (state, { payload }) => {
            state.inputs = payload.data;
        });
        builder.addCase(getInputs.fulfilled, (state, { payload }) => {
            state.inputs = payload.content;
            state.pages = payload.totalPages;
        });
        builder.addCase(updateInput.fulfilled, (state, { payload }) => {
            state.inputs = payload.data;
        });
        builder.addCase(getAllInputs.fulfilled, (state, { payload }) => {
            state.allInputs = payload.content;
        });
    },
});
export const { resetAllInputs } = productInputSlice.actions;
export default productInputSlice.reducer;
