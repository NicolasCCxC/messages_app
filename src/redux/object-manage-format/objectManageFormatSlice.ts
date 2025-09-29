import { IGenericRecord } from '@models/GenericRecord';
import { createSlice } from '@reduxjs/toolkit';
import { deleteObject, getObjectManageFormat, getOneObject } from './actions';

interface IState {
    data: IGenericRecord;
    elements: IGenericRecord[];
    element: IGenericRecord;
    message: string;
}

const initialState: IState = {
    data: {},
    elements: [],
    element: {},
    message: '',
};

const objectManageFormatSlice = createSlice({
    name: 'objectManageFormat',
    initialState,
    reducers: {
        resetElement: state => {
            state.element = {};
        },
    },
    extraReducers: builder => {
        builder
            .addCase(getObjectManageFormat.fulfilled, (state, { payload }) => {
                state.data = payload.data;
                state.elements = payload.elements;
            })
            .addCase(getOneObject.fulfilled, (state, { payload }) => {
                state.element = payload;
            })
            .addCase(deleteObject.fulfilled, (state, { payload }) => {
                state.elements = payload.data;
                state.message = payload.message;
            });
    },
});

export const { resetElement } = objectManageFormatSlice.actions;

export default objectManageFormatSlice.reducer;
