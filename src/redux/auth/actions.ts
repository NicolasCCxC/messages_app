import { createAsyncThunk } from '@reduxjs/toolkit';
import { urls } from '@api/Urls';
import fetchClient from '@api/FetchClient';
import { Login } from '@constants/Login';
import { IGenericRecord } from '@models/GenericRecord';
import LocalStorage from '@utils/LocalStorage';

export const login = createAsyncThunk(
  'auth/login',
  async (credentials: IGenericRecord, { rejectWithValue }) => {
    try {
      const response = await fetchClient.post(urls.auth.login, credentials) as IGenericRecord;

      if (!response?.data?.user) {
        return rejectWithValue(response.message || 'Error de autenticación');
      }

      LocalStorage.set(Login.UserToken, response.data?.accessToken);
      return response.data;

    } catch (error) {
      return rejectWithValue(error);
    }
  }
);
