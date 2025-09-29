/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    createInput,
    deleteInput,
    getInputs,
    updateInput,
    getAllInputs,
  } from './actions';
  
  // ---- Mocks base (antes de importar el SUT) ----
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
      productInput: {
        post: '/product-input',
        delete: (id: string) => `/product-input/${id}`,
        get: (p: any) => `/product-input?${JSON.stringify(p)}`,
        patch: (id: string) => `/product-input/${id}`,
        getAll: (id: string) => `/product-input/all/${id}`,
      },
    },
  }));
  
  jest.mock('@api/ProductInput', () => ({
    __esModule: true,
    apiPostInput: jest.fn(),
    apiDeleteInput: jest.fn(),
    apiGetInputs: jest.fn(),
    apiPatchInput: jest.fn(),
  }));
  
  jest.mock('@utils/Array', () => ({
    __esModule: true,
    addItem: jest.fn((arr: any[], item: any) => [...arr, item]),
    deleteItem: jest.fn((arr: any[], id: string) => arr.filter((x: any) => x.id !== id)),
    replaceItem: jest.fn((arr: any[], item: any) => arr.map((x: any) => (x.id === item.id ? item : x))),
  }));
  
  jest.mock('@utils/Object', () => ({
    __esModule: true,
    removeEmptyStrings: jest.fn((obj: any) => obj),
  }));
  
  jest.mock('@utils/RequestError', () => ({
    __esModule: true,
    extractErrorMessage: jest.fn((e: any) => (e?.message ? e.message : String(e))),
  }));
  
  import {
    apiPostInput,
    apiDeleteInput,
    apiGetInputs,
    apiPatchInput,
  } from '@api/ProductInput';
  import { addItem, deleteItem, replaceItem } from '@utils/Array';
  import { removeEmptyStrings } from '@utils/Object';
  import { extractErrorMessage } from '@utils/RequestError';
  
  // Helper de ejecución de thunk
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
  
  describe('product-input thunks', () => {
    afterEach(() => jest.clearAllMocks());
  
    it('createInput -> addItem con state.productInput.inputs y retorna message', async () => {
      (apiPostInput as jest.Mock).mockResolvedValueOnce({
        data: { id: 'n1', name: 'inputX' },
        message: ['creado'],
      });
  
      const state = { productInput: { inputs: [{ id: 'a' }] } };
      const { dispatch, getState } = createRunner(state);
  
      const result: any = await (createInput({ foo: 'bar' }) as any)(dispatch, getState, undefined);
  
      expect(removeEmptyStrings).toHaveBeenCalled();
      expect(result.type).toBe('productInput/createInput/fulfilled');
      expect(addItem).toHaveBeenCalledWith(state.productInput.inputs, { id: 'n1', name: 'inputX' });
      expect(result.payload).toEqual({
        data: [{ id: 'a' }, { id: 'n1', name: 'inputX' }],
        message: 'creado',
      });
    });
  
    it('createInput -> rejected formateando error', async () => {
      (apiPostInput as jest.Mock).mockRejectedValueOnce(new Error('boom'));
      const { dispatch, getState } = createRunner({ productInput: { inputs: [] } });
      const result: any = await (createInput({}) as any)(dispatch, getState, undefined);
      expect(extractErrorMessage).toHaveBeenCalled();
      expect(result.type).toBe('productInput/createInput/rejected');
      expect(result.payload).toEqual({ data: null, message: 'boom' });
    });
  
    it('deleteInput -> deleteItem con (inputs, data.id) y retorna message', async () => {
      (apiDeleteInput as jest.Mock).mockResolvedValueOnce({
        data: { id: 'b' },
        message: ['ok'],
      });
  
      const state = { productInput: { inputs: [{ id: 'a' }, { id: 'b' }, { id: 'c' }] } };
      const { dispatch, getState } = createRunner(state);
  
      const result: any = await (deleteInput('b') as any)(dispatch, getState, undefined);
  
      expect(result.type).toBe('productInput/deleteInput/fulfilled');
      expect(deleteItem).toHaveBeenCalledWith(state.productInput.inputs, 'b');
      expect(result.payload).toEqual({ data: [{ id: 'a' }, { id: 'c' }], message: 'ok' });
    });
  
    it('deleteInput -> rejected formateando error', async () => {
      (apiDeleteInput as jest.Mock).mockRejectedValueOnce(new Error('nope'));
      const { dispatch, getState } = createRunner({ productInput: { inputs: [] } });
      const result: any = await (deleteInput('x') as any)(dispatch, getState, undefined);
      expect(extractErrorMessage).toHaveBeenCalled();
      expect(result.type).toBe('productInput/deleteInput/rejected');
      expect(result.payload).toEqual({ data: null, message: 'nope' });
    });
  
    it('getInputs -> fulfilled devuelve data', async () => {
      (apiGetInputs as jest.Mock).mockResolvedValueOnce({ data: { content: [{ id: '1' }] } });
      const { dispatch, getState } = createRunner();
      const result: any = await (getInputs({ page: 0 }) as any)(dispatch, getState, undefined);
      expect(result.type).toBe('productInput/getInputs/fulfilled');
      expect(result.payload).toEqual({ content: [{ id: '1' }] });
    });
  
    it('getInputs -> rejected', async () => {
      (apiGetInputs as jest.Mock).mockRejectedValueOnce(new Error('err'));
      const { dispatch, getState } = createRunner();
      const result: any = await (getInputs({}) as any)(dispatch, getState, undefined);
      expect(result.type).toBe('productInput/getInputs/rejected');
      expect(result.payload).toBe('Error: err');
    });
  
    it('updateInput -> replaceItem con state.productInput.inputs y retorna message', async () => {
      (apiPatchInput as jest.Mock).mockResolvedValueOnce({
        data: { id: 'a', name: 'upd' },
        message: ['done'],
      });
  
      const state = { productInput: { inputs: [{ id: 'a', name: 'old' }, { id: 'b' }] } };
      const { dispatch, getState } = createRunner(state);
  
      const result: any = await (updateInput({ id: 'a', name: 'upd' }) as any)(dispatch, getState, undefined);
  
      expect(removeEmptyStrings).toHaveBeenCalled();
      expect(replaceItem).toHaveBeenCalledWith(state.productInput.inputs, { id: 'a', name: 'upd' });
      expect(result.type).toBe('productInput/updateInput/fulfilled');
      expect(result.payload).toEqual({
        data: [{ id: 'a', name: 'upd' }, { id: 'b' }],
        message: 'done',
      });
    });
  
    it('updateInput -> rejected formateando error', async () => {
      (apiPatchInput as jest.Mock).mockRejectedValueOnce(new Error('ouch'));
      const { dispatch, getState } = createRunner({ productInput: { inputs: [] } });
      const result: any = await (updateInput({ id: '1' }) as any)(dispatch, getState, undefined);
      expect(extractErrorMessage).toHaveBeenCalled();
      expect(result.type).toBe('productInput/updateInput/rejected');
      expect(result.payload).toEqual({ data: null, message: 'ouch' });
    });
  
    it('getAllInputs -> fulfilled', async () => {
      (apiGetInputs as jest.Mock).mockResolvedValueOnce({ data: ['a', 'b'] });
      const { dispatch, getState } = createRunner();
      const result: any = await (getAllInputs('p1') as any)(dispatch, getState, undefined);
      expect(result.type).toBe('productInput/getAllInputs/fulfilled');
      expect(result.payload).toEqual(['a', 'b']);
    });
  
    it('getAllInputs -> rejected', async () => {
      (apiGetInputs as jest.Mock).mockRejectedValueOnce(new Error('bad'));
      const { dispatch, getState } = createRunner();
      const result: any = await (getAllInputs('p1') as any)(dispatch, getState, undefined);
      expect(result.type).toBe('productInput/getAllInputs/rejected');
      expect(result.payload).toBe('Error: bad');
    });
  });
  