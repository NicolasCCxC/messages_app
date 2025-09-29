import { createAsyncThunk } from '@reduxjs/toolkit';
import { apiGetProductManagement, apiPatchProductManagement, apiPostProductManagement } from '@api/ProductsManagement';
import { urls } from '@api/Urls';
import { FetchRequest, IParams} from '@models/Request';
import { IProductManagement } from '@pages/product-management';
import { IGenericRecord } from '@models/GenericRecord';

export const getProductManagement = createAsyncThunk(
    'product/getProductManagement',
    async (params: IParams, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.productManagement.get(params));
            // eslint-disable-next-line
            const { data }: any = await apiGetProductManagement(request);
            return data;
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const getAllProducts = createAsyncThunk('product/getAllProducts', async (_, { rejectWithValue }) => {
    try {
        const request = new FetchRequest(urls.productManagement.getEverything);
        // eslint-disable-next-line
        const { data }: any = await apiGetProductManagement(request);
        return data.content.map(({ code, description, ...item }: IGenericRecord) => ({
            ...item,
            label: `${code} - ${description}`,
            value: item.id,
        }));
    } catch (error) {
        return rejectWithValue(String(error));
    }
});

export const createProductManagement = createAsyncThunk(
    'product/createProductManagement',
    async (productData: IProductManagement, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.productManagement.post, productData);
            // eslint-disable-next-line
            const { data, message }: any = await apiPostProductManagement(request);
            return { data, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);

export const modifyProductManagement = createAsyncThunk(
    'product/modifyProductManagement',
    async (productData: IGenericRecord, { rejectWithValue }) => {
        try {
            const request = new FetchRequest(urls.productManagement.patch(productData.id), productData);
            // eslint-disable-next-line
            const { data, message }: any = await apiPatchProductManagement(request);
            return { data, message };
        } catch (error) {
            return rejectWithValue(String(error));
        }
    }
);
