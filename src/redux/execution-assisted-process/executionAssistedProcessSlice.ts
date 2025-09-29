import { IGenericRecord } from '@models/GenericRecord';
import { createSlice } from '@reduxjs/toolkit';
import { getAssistedProcess } from './actions';

interface IState {
    data: IGenericRecord;
    elements: IGenericRecord[];
}

const initialState: IState = {
    data: {},
    elements: [],
};

const executionAssistedProcessSlice = createSlice({
    name: 'executionAssistedProcess',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder.addCase(getAssistedProcess.fulfilled, (state, action) => {
            state.data = action.payload;
            state.elements = action.payload.content || [];
        });
    },
});

export default executionAssistedProcessSlice.reducer;
