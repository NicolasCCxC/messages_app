/* eslint-disable @typescript-eslint/no-explicit-any */
import { getExitPaths, createPath, deletePath, updatePath } from './actions';

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
    exitPaths: {
      get: (p: any) => `/exit?${JSON.stringify(p)}`,
      post: '/exit',
      patch: (id: string) => `/exit/${id}`,
      delete: (id: string) => `/exit/${id}`,
    },
  },
}));

jest.mock('@api/ExitPaths', () => ({
  __esModule: true,
  apiGetPaths: jest.fn(),
  apiPostPath: jest.fn(),
  apiPatchPath: jest.fn(),
  apiDeletePath: jest.fn(),
}));

jest.mock('@utils/Array', () => ({
  __esModule: true,
  addItem: jest.fn((arr: any[], item: any) => [...arr, item]),
  deleteItem: jest.fn((arr: any[], id: string) => arr.filter((x: any) => x.id !== id)),
  replaceItem: jest.fn((arr: any[], item: any) => arr.map((x: any) => (x.id === item.id ? item : x))),
}));

import { apiGetPaths, apiPostPath, apiPatchPath, apiDeletePath } from '@api/ExitPaths';
import { addItem, deleteItem, replaceItem } from '@utils/Array';

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

describe('exit-paths thunks', () => {
  afterEach(() => jest.clearAllMocks());

  it('getExitPaths fulfilled', async () => {
    (apiGetPaths as jest.Mock).mockResolvedValueOnce({ data: { content: [{ id: '1' }] } });
    const { dispatch, getState } = createRunner();
    const result: any = await (getExitPaths({ page: 0 }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('paths/getExitPaths/fulfilled');
    expect(result.payload).toEqual({ content: [{ id: '1' }] });
  });

  it('createPath agrega con addItem y retorna message', async () => {
    (apiPostPath as jest.Mock).mockResolvedValueOnce({ data: { id: 'n' }, message: ['ok'] });
    const state = { paths: { paths: [{ id: 'a' }] } };
    const { dispatch, getState } = createRunner(state);
    const result: any = await (createPath({ name: 'x' }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('paths/createPath/fulfilled');
    expect(addItem).toHaveBeenCalledWith(state.paths.paths, { id: 'n' });
    expect(result.payload).toEqual({ data: [{ id: 'a' }, { id: 'n' }], message: 'ok' });
  });

  it('deletePath filtra con deleteItem y retorna message', async () => {
    (apiDeletePath as jest.Mock).mockResolvedValueOnce({ data: { id: 'b' }, message: ['bye'] });
    const state = { paths: { paths: [{ id: 'a' }, { id: 'b' }, { id: 'c' }] } };
    const { dispatch, getState } = createRunner(state);
    const result: any = await (deletePath('b') as any)(dispatch, getState, undefined);
    expect(result.type).toBe('paths/deletePath/fulfilled');
    expect(deleteItem).toHaveBeenCalledWith(state.paths.paths, 'b');
    expect(result.payload).toEqual({ data: [{ id: 'a' }, { id: 'c' }], message: 'bye' });
  });

  it('updatePath reemplaza con replaceItem', async () => {
    (apiPatchPath as jest.Mock).mockResolvedValueOnce({ data: { id: 'a', path: '/new' }, message: ['upd'] });
    const state = { paths: { paths: [{ id: 'a', path: '/old' }, { id: 'b', path: '/b' }] } };
    const { dispatch, getState } = createRunner(state);
    const result: any = await (updatePath({ id: 'a', path: '/new' }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('paths/updatePath/fulfilled');
    expect(replaceItem).toHaveBeenCalledWith(state.paths.paths, { id: 'a', path: '/new' });
    expect(result.payload).toEqual({ data: [{ id: 'a', path: '/new' }, { id: 'b', path: '/b' }], message: 'upd' });
  });
});
