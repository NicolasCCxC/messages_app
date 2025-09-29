import { createSlice } from '@reduxjs/toolkit';
import { IGenericRecord } from '@models/GenericRecord';
import { createProductManagement, getAllProducts, getProductManagement, modifyProductManagement } from './actions';

interface IProductManagementState {
    data: IGenericRecord;
    products: IGenericRecord[];
    error: string | null;
    message: string;
    status: 'idle' | 'succeeded' | 'failed';
    allProducts: IGenericRecord[];
}

const initialState: IProductManagementState = {
    data: {},
    products: [],
    error: null,
    status: 'idle',
    message: '',
    allProducts: [],
};

const productManagementSlice = createSlice({
    name: 'productManagement',
    initialState,
    reducers: {},
    extraReducers: builder => {
        builder
            .addCase(getProductManagement.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.data = action.payload;
                state.products = action.payload.content;
            })
            .addCase(createProductManagement.fulfilled, (state, action) => {
                state.status = 'succeeded';
                if (state?.products?.length >= 10) state?.products?.pop();
                state.data = { ...state.data, content: [action.payload.data, ...state.data.content] };
                state.products = [action.payload.data, ...state.products];
                state.message = action.payload.message;
                state.error = null;
            })
            .addCase(modifyProductManagement.fulfilled, (state, action) => {
                state.status = 'succeeded';
                const newData = state.products.map(product =>
                    product.id === action.payload.data.id ? action.payload.data : product
                );
                state.products = newData;
                state.message = action.payload.message;
            })
            .addCase(getProductManagement.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.payload as string;
            })
            .addCase(createProductManagement.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.payload as string;
            })
            .addCase(modifyProductManagement.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.payload as string;
            });
        builder.addCase(getAllProducts.fulfilled, (state, action) => {
            state.allProducts = action.payload;
        });
    },
});

export default productManagementSlice.reducer;
