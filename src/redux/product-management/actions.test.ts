/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    getProductManagement,
    getAllProducts,
    createProductManagement,
    modifyProductManagement,
  } from './actions';
  
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
      productManagement: {
        get: (p: any) => `/pm?${JSON.stringify(p)}`,
        getEverything: '/pm/all',
        post: '/pm',
        patch: (id: string) => `/pm/${id}`,
      },
    },
  }));
  
  jest.mock('@api/ProductsManagement', () => ({
    __esModule: true,
    apiGetProductManagement: jest.fn(),
    apiPostProductManagement: jest.fn(),
    apiPatchProductManagement: jest.fn(),
  }));
  
  import {
    apiGetProductManagement,
    apiPostProductManagement,
    apiPatchProductManagement,
  } from '@api/ProductsManagement';
  
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
  
  describe('product-management thunks', () => {
    afterEach(() => jest.clearAllMocks());
  
    it('getProductManagement -> fulfilled', async () => {
      (apiGetProductManagement as jest.Mock).mockResolvedValueOnce({ data: { content: [{ id: '1' }], total: 1 } });
      const { dispatch, getState } = createRunner();
      const result: any = await (getProductManagement({ page: 0 } as any) as any)(dispatch, getState, undefined);
      expect(result.type).toBe('product/getProductManagement/fulfilled');
      expect(result.payload).toEqual({ content: [{ id: '1' }], total: 1 });
    });
  
    it('getProductManagement -> rejected', async () => {
      (apiGetProductManagement as jest.Mock).mockRejectedValueOnce(new Error('boom'));
      const { dispatch, getState } = createRunner();
      const result: any = await (getProductManagement({} as any) as any)(dispatch, getState, undefined);
      expect(result.type).toBe('product/getProductManagement/rejected');
      expect(result.payload).toBe('Error: boom');
    });
  
    it('getAllProducts -> mapea {label, value}', async () => {
      (apiGetProductManagement as jest.Mock).mockResolvedValueOnce({
        data: {
          content: [
            { id: 'p1', code: '001', description: 'Cuenta', extra: 'x' },
            { id: 'p2', code: '002', description: 'Tarjeta' },
          ],
        },
      });
  
      const { dispatch, getState } = createRunner();
      const result: any = await (getAllProducts() as any)(dispatch, getState, undefined);
  
      expect(result.type).toBe('product/getAllProducts/fulfilled');
      expect(result.payload).toEqual([
        { id: 'p1', extra: 'x', label: '001 - Cuenta', value: 'p1' },
        { id: 'p2', label: '002 - Tarjeta', value: 'p2' },
      ]);
    });
  
    it('createProductManagement -> fulfilled', async () => {
      (apiPostProductManagement as jest.Mock).mockResolvedValueOnce({ data: { id: 'n' }, message: 'ok' });
      const { dispatch, getState } = createRunner();
      const result: any = await (createProductManagement({ name: 'prod' } as any) as any)(
        dispatch,
        getState,
        undefined
      );
      expect(result.type).toBe('product/createProductManagement/fulfilled');
      expect(result.payload).toEqual({ data: { id: 'n' }, message: 'ok' });
    });
  
    it('modifyProductManagement -> fulfilled', async () => {
      (apiPatchProductManagement as jest.Mock).mockResolvedValueOnce({ data: { id: 'p1' }, message: 'upd' });
      const { dispatch, getState } = createRunner();
      const result: any = await (modifyProductManagement({ id: 'p1', a: 1 } as any) as any)(
        dispatch,
        getState,
        undefined
      );
      expect(result.type).toBe('product/modifyProductManagement/fulfilled');
      expect(result.payload).toEqual({ data: { id: 'p1' }, message: 'upd' });
    });
  });
  