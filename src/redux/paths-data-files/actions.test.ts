/* eslint-disable @typescript-eslint/no-explicit-any */
import { getPathsDataFile, createPathDataFile, modifyPathDataFile } from './actions';

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
    pathsDataFiles: {
      get: (p: any) => `/pdfiles?${JSON.stringify(p)}`,
      post: '/pdfiles',
      patch: (id: string) => `/pdfiles/${id}`,
    },
  },
}));

jest.mock('@api/PathsDataFiles', () => ({
  __esModule: true,
  apiGetPathsDataFiles: jest.fn(),
  apiPostPathsDataFiles: jest.fn(),
  apiPatchPathsDataFiles: jest.fn(),
}));

jest.mock('@constants/MaxAndMinValues', () => ({
  __esModule: true,
  MAX_TABLE_ITEMS: 10,
  MIN_TABLE_ITEMS: 0,
}));

import { apiGetPathsDataFiles, apiPostPathsDataFiles, apiPatchPathsDataFiles } from '@api/PathsDataFiles';

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

describe('paths-data-files thunks', () => {
  afterEach(() => jest.clearAllMocks());

  it('getPathsDataFile mapea product.id', async () => {
    (apiGetPathsDataFiles as jest.Mock).mockResolvedValueOnce({
      data: {
        content: [
          { id: '1', product: { id: 'p1' } },
          { id: '2', product: { id: 'p2' } },
        ],
        totalPages: 3,
      },
    });

    const { dispatch, getState } = createRunner();
    const result: any = await (getPathsDataFile({ page: 0 }) as any)(dispatch, getState, undefined);

    expect(result.type).toBe('pathsDataFile/getPathsDataFile/fulfilled');
    expect(result.payload).toEqual({
      content: [
        { id: '1', product: 'p1' },
        { id: '2', product: 'p2' },
      ],
      totalPages: 3,
    });
  });

  it('createPathDataFile coloca nuevo al inicio y desactiva similares activos del mismo producto', async () => {
    (apiPostPathsDataFiles as jest.Mock).mockResolvedValueOnce({
      data: { id: '4', product: { id: 'p1' }, active: true },
      message: 'created',
    });

    const state = {
      pathsDataFiles: {
        paths: [
          { id: '1', product: 'p1', active: true },
          { id: '2', product: 'p1', active: true },
          { id: '3', product: 'p2', active: false },
        ],
      },
    };

    const { dispatch, getState } = createRunner(state);

    const result: any = await (createPathDataFile({ routeEntry: '/x' }) as any)(dispatch, getState, undefined);

    expect(result.type).toBe('pathsDataFile/createPathDataFile/fulfilled');
    expect(result.payload.message).toBe('created');

    // primero es el nuevo con product mapeado
    expect(result.payload.content[0]).toEqual({ id: '4', product: 'p1', active: true });

    // los que compartían producto p1 quedan inactivos
    const rest = result.payload.content.slice(1);
    const byId = Object.fromEntries(rest.map((r: any) => [r.id, r]));
    expect(byId['1'].active).toBe(false);
    expect(byId['2'].active).toBe(false);
    // distinto producto no cambia (seguía false)
    expect(byId['3'].active).toBe(false);
  });

  it('modifyPathDataFile actualiza y aplica regla de similitud', async () => {
    (apiPatchPathsDataFiles as jest.Mock).mockResolvedValueOnce({
      data: { id: '2', product: { id: 'p1' }, active: true },
      message: 'updated',
    });

    const state = {
      pathsDataFiles: {
        paths: [
          { id: '1', product: 'p1', active: true },
          { id: '2', product: 'p1', active: false },
          { id: '3', product: 'p2', active: true },
        ],
      },
    };

    const { dispatch, getState } = createRunner(state);

    const result: any = await (modifyPathDataFile({ id: '2', active: true }) as any)(
      dispatch,
      getState,
      undefined
    );

    expect(result.type).toBe('product/modifyPathDataFile/fulfilled');
    expect(result.payload.message).toBe('updated');

    // item 2 actualizado y activo
    const updated = result.payload.content.find((x: any) => x.id === '2');
    expect(updated).toEqual({ id: '2', product: 'p1', active: true });

    // item 1 mismo producto y distinto id queda inactivo
    const item1 = result.payload.content.find((x: any) => x.id === '1');
    expect(item1?.active).toBe(false);

    // item 3 producto distinto permanece activo
    const item3 = result.payload.content.find((x: any) => x.id === '3');
    expect(item3?.active).toBe(true);
  });
});
