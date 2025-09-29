import { IGenericRecord } from '@models/GenericRecord';
import { createSlice } from '@reduxjs/toolkit';
import { createIndex, getIndex } from './actions';

interface IState {
    data: IGenericRecord;
    elements: IGenericRecord[];
}

const initialState: IState = {
    data: {},
    elements: [],
};

const executingIndexGenerationSlice = createSlice({
    name: 'executingIndexGeneration',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder
            .addCase(createIndex.fulfilled, (state, action) => {
                state.elements = action.payload.elements;
            })
            .addCase(getIndex.fulfilled, (state, action) => {
                state.data = action.payload;
                state.elements = action.payload.content || [];
            });
    },
});

export default executingIndexGenerationSlice.reducer;
