import { createAssistedProcess, getAssistedProcess } from './actions';

// ---- Mocks ----
jest.mock('@api/Urls', () => ({
    __esModule: true,
    urls: {
        executingAssistedProcess: {
            post: '/assisted',
            get: (_: any) => '/assisted',
        },
    },
}));

const apiPostAssistedProcess = jest.fn();
const apiGetAssistedProcess = jest.fn();

jest.mock('@api/ExecutionAssistedProcess', () => ({
    __esModule: true,
    apiPostAssistedProcess: (...a: any[]) => apiPostAssistedProcess(...a),
    apiGetAssistedProcess: (...a: any[]) => apiGetAssistedProcess(...a),
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

describe('execution-assisted-process thunks', () => {
    beforeEach(() => jest.clearAllMocks());

    it('createAssistedProcess retorna data y mensaje', async () => {
        const newItem = { id: 'A1' };
        apiPostAssistedProcess.mockResolvedValue({ data: newItem, message: 'ok' });

        const base = Array.from({ length: 10 }).map((_, i) => ({ id: `x${i}` }));
        const { dispatch, getState, extra } = makeThunkCtx({
            executionAssistedProcess: { elements: base },
        });

        const result = await createAssistedProcess({ productId: 'X', period: '202501' } as any)(dispatch, getState, extra);

        expect(result.type).toMatch(/\/fulfilled$/);
        const payload = (result as any).payload;
        expect(payload.message).toBe('ok');
        expect(payload.data).toEqual(newItem);
    });

    it('getAssistedProcess retorna data del api', async () => {
        apiGetAssistedProcess.mockResolvedValue({ data: { content: [3], totalPages: 2 } });

        const { dispatch, getState, extra } = makeThunkCtx();
        const result = await getAssistedProcess({} as any)(dispatch, getState, extra);

        expect(result.type).toMatch(/\/fulfilled$/);
        const payload = (result as any).payload;
        expect(payload).toEqual({ content: [3], totalPages: 2 });
    });

    it('createAssistedProcess rejected en error', async () => {
        apiPostAssistedProcess.mockRejectedValue(new Error('nope'));

        const { dispatch, getState, extra } = makeThunkCtx({
            executionAssistedProcess: { elements: [] },
        });

        const result = await createAssistedProcess({} as any)(dispatch, getState, extra);
        expect(result.type).toMatch(/\/rejected$/);
        expect((result as any).payload).toBe('Error: nope');
    });
});
