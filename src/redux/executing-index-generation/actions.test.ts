import { createIndex, getIndex } from './actions';

// ---- Mocks de dependencias ----
jest.mock('@api/Urls', () => ({
  __esModule: true,
  urls: {
    executingIndexGeneration: {
      post: '/exec-index',
      get: (_params: any) => '/exec-index',
    },
  },
}));

const apiPostIndex = jest.fn();
const apiGetIndex = jest.fn();

jest.mock('@api/ExecutingIndexGeneration', () => ({
  __esModule: true,
  apiPostIndex: (...args: any[]) => apiPostIndex(...args),
  apiGetIndex: (...args: any[]) => apiGetIndex(...args),
}));

// Mock de FetchRequest: retorna un objeto plano (evita usar `this`)
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

describe('executing-index-generation thunks', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('createIndex agrega al inicio y limita a 10', async () => {
    const newItem = { id: 'n1' };
    apiPostIndex.mockResolvedValue({ data: newItem, message: 'ok' });

    const base = Array.from({ length: 10 }).map((_, i) => ({ id: `e${i}` }));
    const { dispatch, getState, extra } = makeThunkCtx({
      executingIndexGeneration: { elements: base },
    });

    const result = await createIndex({ productId: 'X', period: '202501' } as any)(
      dispatch,
      getState,
      extra
    );

    expect(result.type).toMatch(/\/fulfilled$/);

    const payload = (result as any).payload;
    expect(payload.message).toBe('ok');
    expect(payload.elements).toHaveLength(10);
    expect(payload.elements[0]).toEqual(newItem);
  });

  it('getIndex retorna data del api', async () => {
    apiGetIndex.mockResolvedValue({ data: { content: [1, 2], totalPages: 4 } });

    const { dispatch, getState, extra } = makeThunkCtx();
    const result = await getIndex({ page: 0 } as any)(dispatch, getState, extra);

    expect(result.type).toMatch(/\/fulfilled$/);
    const payload = (result as any).payload;
    expect(payload).toEqual({ content: [1, 2], totalPages: 4 });
  });

  it('createIndex rejected en error', async () => {
    apiPostIndex.mockRejectedValue(new Error('boom'));

    const { dispatch, getState, extra } = makeThunkCtx({
      executingIndexGeneration: { elements: [] },
    });

    const result = await createIndex({} as any)(dispatch, getState, extra);
    expect(result.type).toMatch(/\/rejected$/);
    expect((result as any).payload).toBe('Error: boom');
  });
});
