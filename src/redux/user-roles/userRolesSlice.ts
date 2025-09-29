// src/redux/user-roles/userRolesSlice.ts
import { createSlice } from '@reduxjs/toolkit';
import type { IGenericRecord } from '@models/GenericRecord';
import { getUserRoles, updateRole } from './actions';

interface IRolesState {
  allData: IGenericRecord[];
  pages?: number;
  error: string;
  message?: string;
}

const initialState: IRolesState = {
  allData: [],
  error: '',
  pages: 1,
  message: '',
};

const userRolesSlice = createSlice({
  name: 'roles',
  initialState,
  reducers: {},
  extraReducers: builder => {
    builder
      .addCase(getUserRoles.fulfilled, (state, { payload }: any) => {
        // Soporta dos formas de payload: array simple o objeto con { content, totalPages }
        if (Array.isArray(payload)) {
          state.allData = payload;
          // si tu API no trae paginado en este caso, puedes dejar pages como está
        } else {
          state.allData = payload?.content ?? [];
          if (typeof payload?.totalPages === 'number') state.pages = payload.totalPages;
        }
        state.error = '';
      })
      .addCase(getUserRoles.rejected, (state, action: any) => {
        state.error = String(action.payload ?? action.error?.message ?? 'Error');
      })
      .addCase(updateRole.fulfilled, (state, { payload }: any) => {
        // El thunk devuelve { data: replaceItem(state.allData, data), message }
        state.allData = payload.data ?? state.allData;
        state.message = payload.message ?? '';
        state.error = '';
      })
      .addCase(updateRole.rejected, (state, action: any) => {
        state.error = String(action.payload ?? action.error?.message ?? 'Error');
      });
  },
});

export default userRolesSlice.reducer;
