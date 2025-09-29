/* eslint-disable @typescript-eslint/no-explicit-any */
import { createSlice } from '@reduxjs/toolkit';
import { login } from './actions';

interface IState {
    user: any;
    token: string;
    error: string;
}

const initialState: IState = {
    user: {},
    token: '',
    error: '',
};

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        deleteToken: state => {
            state.token = '';
        },
        cleanError: state => {
            state.error = '';
        },
    },
    extraReducers: builder => {
        builder
            .addCase(login.fulfilled, (state, action) => {
                state.user = action.payload.user;
                state.token = action.payload.accessToken;
                state.error = '';
            })
            .addCase(login.rejected, (state, action) => {
                state.error = String(action.payload);
            });
    },
});

export const { cleanError, deleteToken } = authSlice.actions;

export default authSlice.reducer;
