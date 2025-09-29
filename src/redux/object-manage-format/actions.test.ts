/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    getObjectManageFormat,
    getOneObject,
    createObjectManageFormat,
    deleteObject,
    modifyObjectManageFormat,
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
      objectManageFormat: {
        get: (p: any) => `/omf?${JSON.stringify(p)}`,
        getOne: (id: string) => `/omf/${id}`,
        post: '/omf',
        patch: (id: string) => `/omf/${id}`,
        delete: (id: string) => `/omf/${id}`,
      },
    },
  }));
  
  jest.mock('@api/ObjectManageFormat', () => ({
    __esModule: true,
    apiGetObjectManageFormat: jest.fn(),
    apiPostObjectManageFormat: jest.fn(),
    apiPatchObjectManageFormat: jest.fn(),
    apiDeleteObjectManageFormat: jest.fn(),
  }));
  
  jest.mock('@utils/Array', () => ({
    __esModule: true,
    deleteItem: jest.fn((arr: any[], id: string) => arr.filter((x: any) => x.id !== id)),
  }));
  
  jest.mock('@utils/RequestError', () => ({
    __esModule: true,
    extractErrorMessage: jest.fn((e: any) => (e?.message ? e.message : String(e))),
  }));
  
  import {
    apiGetObjectManageFormat,
    apiPostObjectManageFormat,
    apiPatchObjectManageFormat,
    apiDeleteObjectManageFormat,
  } from '@api/ObjectManageFormat';
  import { deleteItem } from '@utils/Array';
  import { extractErrorMessage } from '@utils/RequestError';
  
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
  
  describe('object-manage-format thunks', () => {
    afterEach(() => {
      jest.clearAllMocks();
    });
  
    it('getObjectManageFormat mapea elementos', async () => {
      (apiGetObjectManageFormat as jest.Mock).mockResolvedValueOnce({
        data: {
          content: [
            { id: '1', product: { id: 'p1' }, name: 'obj1' },
            { id: '2', product: { id: 'p2' }, name: 'obj2' },
          ],
          page: 0,
        },
      });
  
      const { dispatch, getState } = createRunner();
      const result: any = await (getObjectManageFormat({ page: 0 }) as any)(dispatch, getState, undefined);
  
      expect(result.type).toBe('objectManageFormat/getObjectManageFormat/fulfilled');
      expect(result.payload).toEqual({
        data: { content: [{ id: '1', product: { id: 'p1' }, name: 'obj1' }, { id: '2', product: { id: 'p2' }, name: 'obj2' }], page: 0 },
        elements: [
          { id: '1', product: 'p1', objectName: 'obj1', name: 'obj1' },
          { id: '2', product: 'p2', objectName: 'obj2', name: 'obj2' },
        ],
      });
    });
  
    it('getOneObject devuelve data', async () => {
      (apiGetObjectManageFormat as jest.Mock).mockResolvedValueOnce({ data: { id: 'x' } });
      const { dispatch, getState } = createRunner();
      const result: any = await (getOneObject('x') as any)(dispatch, getState, undefined);
      expect(result.type).toBe('objectManageFormat/getOneObject/fulfilled');
      expect(result.payload).toEqual({ id: 'x' });
    });
  
    it('createObjectManageFormat devuelve {data, message}', async () => {
      (apiPostObjectManageFormat as jest.Mock).mockResolvedValueOnce({
        data: { id: 'n' },
        message: 'ok',
      });
      const { dispatch, getState } = createRunner();
      const result: any = await (createObjectManageFormat({ a: 1 }) as any)(dispatch, getState, undefined);
      expect(result.type).toBe('objectManageFormat/createObjectManageFormat/fulfilled');
      expect(result.payload).toEqual({ data: { id: 'n' }, message: 'ok' });
    });
  
    it('deleteObject filtra y devuelve message', async () => {
      (apiDeleteObjectManageFormat as jest.Mock).mockResolvedValueOnce({ message: ['bye'] });
  
      const state = { objectManageFormat: { elements: [{ id: '1' }, { id: '2' }, { id: '3' }] } };
      const { dispatch, getState } = createRunner(state);
  
      const result: any = await (deleteObject('2') as any)(dispatch, getState, undefined);
      expect(result.type).toBe('objectManageFormat/deleteObject/fulfilled');
      expect(deleteItem).toHaveBeenCalledWith(state.objectManageFormat.elements, '2');
      expect(result.payload.message).toBe('bye');
      expect(result.payload.data).toEqual([{ id: '1' }, { id: '3' }]);
    });
  
    it('deleteObject rejected retorna {data:null, message:extractErrorMessage}', async () => {
      (apiDeleteObjectManageFormat as jest.Mock).mockRejectedValueOnce(new Error('boom'));
      const { dispatch, getState } = createRunner({ objectManageFormat: { elements: [] } });
      const result: any = await (deleteObject('x') as any)(dispatch, getState, undefined);
      expect(result.type).toBe('objectManageFormat/deleteObject/rejected');
      expect(extractErrorMessage).toHaveBeenCalled();
      expect(result.payload).toEqual({ data: null, message: 'boom' });
    });
  
    it('modifyObjectManageFormat fulfilled', async () => {
      (apiPatchObjectManageFormat as jest.Mock).mockResolvedValueOnce({
        data: { id: '3', foo: 'bar' },
        message: 'upd',
      });
      const { dispatch, getState } = createRunner();
      const result: any = await (modifyObjectManageFormat({ diff: { foo: 'bar' }, id: '3' }) as any)(
        dispatch,
        getState,
        undefined
      );
      expect(result.type).toBe('objectManageFormat/modifyObjectManageFormat/fulfilled');
      expect(result.payload).toEqual({ data: { id: '3', foo: 'bar' }, message: 'upd' });
    });
  });
  