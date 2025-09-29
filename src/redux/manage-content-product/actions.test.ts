/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    getManageContentProduct,
    createManageContentProduct,
    modifyManageContentProduct,
    deleteContentProduct,
  } from './actions';
  
  // ---- Mocks compartidos ----
  jest.mock('@models/Request', () => ({
    __esModule: true,
    FetchRequest: class FetchRequest {
      resource: any;
      body: any;
      constructor(resource: any, body?: any) {
        this.resource = resource;
        this.body = body;
      }
    },
  }));
  
  jest.mock('@api/Urls', () => ({
    __esModule: true,
    urls: {
      manageContentProduct: {
        get: (p: any) => `/mcp?${JSON.stringify(p)}`,
        post: '/mcp',
        patch: (id: string) => `/mcp/${id}`,
        delete: (id: string) => `/mcp/${id}`,
      },
    },
  }));
  
  jest.mock('@api/ManageContentProduct', () => ({
    __esModule: true,
    apiGetManageContentProduct: jest.fn(),
    apiPostManageContentProduct: jest.fn(),
    apiPatchManageContentProduct: jest.fn(),
    apiDeleManageContentProduct: jest.fn(),
  }));
  
  jest.mock('@utils/Array', () => ({
    __esModule: true,
    deleteItem: jest.fn((arr: any[], id: string) => arr.filter((x: any) => x.id !== id)),
  }));
  
  jest.mock('@constants/MaxAndMinValues', () => ({
    __esModule: true,
    MAX_TABLE_ITEMS: 10,
    MIN_TABLE_ITEMS: 0,
  }));
  
  jest.mock('@utils/GetRequiredFields', () => ({
    __esModule: true,
    getRequiredFields: jest.fn(() => 'MAPPED_FIELDS'),
  }));
  
  import {
    apiGetManageContentProduct,
    apiPostManageContentProduct,
    apiPatchManageContentProduct,
    apiDeleManageContentProduct,
  } from '@api/ManageContentProduct';
  import { deleteItem } from '@utils/Array';
  import { getRequiredFields } from '@utils/GetRequiredFields';
  
  const createRunner = (state: any = {}) => {
    const dispatched: any[] = [];
    const dispatch = (action: any) => {
      dispatched.push(action);
      if (typeof action === 'function') {
        return action(dispatch, () => state, undefined);
      }
      return action;
    };
    const getState = () => state;
    return { dispatch, getState, dispatched };
  };
  
  describe('manage-content-product thunks', () => {
    afterEach(() => {
      jest.clearAllMocks();
    });
  
    it('getManageContentProduct mapea content (product -> id, mappedRequiredFields)', async () => {
      (apiGetManageContentProduct as jest.Mock).mockResolvedValueOnce({
        data: {
          content: [
            { id: 'a', product: { id: 'p1' }, requiredFields: [{ id: 1 }] },
            { id: 'b', product: { id: 'p2' }, requiredFields: [{ id: 2 }] },
          ],
          totalPages: 5,
        },
      });
  
      const { dispatch, getState } = createRunner();
      const result: any = await (getManageContentProduct({ page: 0 }) as any)(dispatch, getState, undefined);
  
      expect(result.type).toBe('manageContentProduct/getManageContentProduct/fulfilled');
      expect(getRequiredFields).toHaveBeenCalled();
      expect(result.payload).toEqual({
        content: [
          { id: 'a', product: 'p1', mappedRequiredFields: 'MAPPED_FIELDS', requiredFields: [{ id: 1 }] },
          { id: 'b', product: 'p2', mappedRequiredFields: 'MAPPED_FIELDS', requiredFields: [{ id: 2 }] },
        ],
        totalPages: 5,
      });
    });
  
    it('createManageContentProduct agrega el nuevo al inicio y hace slice del resto', async () => {
      (apiPostManageContentProduct as jest.Mock).mockResolvedValueOnce({
        data: { id: 'new', product: { id: 'pX' }, requiredFields: [] },
        message: 'created',
      });
  
      const prevContent = Array.from({ length: 8 }, (_, i) => ({ id: `r${i}` }));
      const state = { manageContentProduct: { content: prevContent } };
  
      const { dispatch, getState } = createRunner(state);
  
      const result: any = await (createManageContentProduct({ foo: 'bar' }) as any)(
        dispatch,
        getState,
        undefined
      );
  
      expect(result.type).toBe('manageContentProduct/createManageContentProduct/fulfilled');
      expect(result.payload.message).toBe('created');
      // nuevo primero + slice (0..10) de 8 elementos => 9 total
      expect(result.payload.content[0]).toEqual({
        id: 'new',
        product: 'pX',
        mappedRequiredFields: 'MAPPED_FIELDS',
        requiredFields: [],
      });
      expect(result.payload.content).toHaveLength(9);
    });
  
    it('modifyManageContentProduct reemplaza el item por id y mapea', async () => {
      (apiPatchManageContentProduct as jest.Mock).mockResolvedValueOnce({
        data: { id: 'b', product: { id: 'p9' }, requiredFields: [{ id: 9 }] },
        message: 'updated',
      });
  
      const state = {
        manageContentProduct: {
          content: [
            { id: 'a', product: 'p1' },
            { id: 'b', product: 'p2' },
            { id: 'c', product: 'p3' },
          ],
        },
      };
  
      const { dispatch, getState } = createRunner(state);
      const result: any = await (modifyManageContentProduct({ formData: { x: 1 }, id: 'b' }) as any)(
        dispatch,
        getState,
        undefined
      );
  
      expect(result.type).toBe('manageContentProduct/modifyManageContentProduct/fulfilled');
      expect(result.payload.message).toBe('updated');
      expect(result.payload.content).toEqual([
        { id: 'a', product: 'p1' },
        { id: 'b', product: 'p9', mappedRequiredFields: 'MAPPED_FIELDS', requiredFields: [{ id: 9 }] },
        { id: 'c', product: 'p3' },
      ]);
    });
  
    it('deleteContentProduct retorna lista filtrada y message del API', async () => {
      (apiDeleManageContentProduct as jest.Mock).mockResolvedValueOnce({
        message: ['deleted ok'],
      });
  
      const state = {
        manageContentProduct: {
          content: [
            { id: '1' },
            { id: '2' },
            { id: '3' },
          ],
        },
      };
  
      const { dispatch, getState } = createRunner(state);
      const result: any = await (deleteContentProduct('2') as any)(dispatch, getState, undefined);
  
      expect(result.type).toBe('productInput/deleteContentProduct/fulfilled');
      expect(deleteItem).toHaveBeenCalledWith(state.manageContentProduct.content, '2');
      expect(result.payload.message).toBe('deleted ok');
      expect(result.payload.data).toEqual([{ id: '1' }, { id: '3' }]);
    });
  
    it('getManageContentProduct rejected on error', async () => {
      (apiGetManageContentProduct as jest.Mock).mockRejectedValueOnce(new Error('boom'));
      const { dispatch, getState } = createRunner();
      const result: any = await (getManageContentProduct({}) as any)(dispatch, getState, undefined);
      expect(result.type).toBe('manageContentProduct/getManageContentProduct/rejected');
      expect(result.payload).toBe('Error: boom');
    });
  });
  