import { cancelFile, createFile, getFile } from './actions';

// ---- Mocks ----
jest.mock('@api/Urls', () => ({
  __esModule: true,
  urls: {
    inputFileUpload: {
      post: '/file',
      get: (_: any) => '/file',
      cancelPost: (id: string) => `/file/${id}/cancel`,
    },
  },
}));

const apiPostFile = jest.fn();
const apiGetFile = jest.fn();

jest.mock('@api/inputFileUpload', () => ({
  __esModule: true,
  apiPostFile: (...a: any[]) => apiPostFile(...a),
  apiGetFile: (...a: any[]) => apiGetFile(...a),
}));

// Mock de FetchRequest que retorna objeto plano
jest.mock('@models/Request', () => ({
  __esModule: true,
  FetchRequest: function (resource: any, body?: any) {
    return {
      resource: typeof resource === 'function' ? resource({}) : resource,
      body,
    };
  },
}));

// ---- Helpers ----
const makeThunkCtx = (state: any = {}) => {
  const dispatch = jest.fn();
  const getState = jest.fn(() => state);
  const extra: any = undefined;
  return { dispatch, getState, extra };
};

describe('input-file-upload thunks', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('createFile agrega (mapeando productName y userName) al inicio y limita a 10', async () => {
    const apiItem = {
      id: 'F1',
      product: { description: 'Producto X' },
      user: { name: 'Juan' },
    };
    apiPostFile.mockResolvedValue({ data: apiItem, message: 'ok' });

    const base = Array.from({ length: 10 }).map((_, i) => ({ id: `r${i}` }));
    const { dispatch, getState, extra } = makeThunkCtx({
      inputFileUpload: { elements: base },
    });

    const result = await createFile({ productId: 'P', period: '202501' } as any)(
      dispatch,
      getState,
      extra
    );

    expect(result.type).toMatch(/\/fulfilled$/);

    const payload = (result as any).payload;
    expect(payload.message).toBe('ok');
    expect(payload.elements).toHaveLength(10);
    expect(payload.elements[0]).toEqual({
      ...apiItem,
      productName: 'Producto X',
      userName: 'Juan',
    });
  });

  it('getFile mapea content a productName y userName', async () => {
    apiGetFile.mockResolvedValue({
      data: {
        content: [
          {
            id: '1',
            product: { description: 'Prod 1' },
            user: { name: 'Ana' },
          },
          {
            id: '2',
            product: { description: 'Prod 2' },
            user: { name: 'Luis' },
          },
        ],
        totalPages: 3,
      },
    });

    const { dispatch, getState, extra } = makeThunkCtx();
    const result = await getFile({} as any)(dispatch, getState, extra);

    expect(result.type).toMatch(/\/fulfilled$/);

    const payload = (result as any).payload;
    expect(payload.data.totalPages).toBe(3);
    expect(payload.content).toEqual([
      { id: '1', product: { description: 'Prod 1' }, user: { name: 'Ana' }, productName: 'Prod 1', userName: 'Ana' },
      { id: '2', product: { description: 'Prod 2' }, user: { name: 'Luis' }, productName: 'Prod 2', userName: 'Luis' },
    ]);
  });

  it('cancelFile retorna {data, message} del api', async () => {
    apiPostFile.mockResolvedValue({ data: { id: 'X' }, message: 'cancelado' });

    const { dispatch, getState, extra } = makeThunkCtx();
    const result = await cancelFile('abc')(dispatch, getState, extra);

    expect(result.type).toMatch(/\/fulfilled$/);
    expect((result as any).payload).toEqual({ data: { id: 'X' }, message: 'cancelado' });
  });

  it('createFile rejected en error', async () => {
    apiPostFile.mockRejectedValue(new Error('oops'));
    const { dispatch, getState, extra } = makeThunkCtx({
      inputFileUpload: { elements: [] },
    });

    const result = await createFile({} as any)(dispatch, getState, extra);
    expect(result.type).toMatch(/\/rejected$/);
    expect((result as any).payload).toBe('Error: oops');
  });
});
