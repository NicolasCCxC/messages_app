/* eslint-disable @typescript-eslint/no-explicit-any */
import { activateFormat, createFormat, getFormats, getProductObjects, updateFormat } from './actions';

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
    pdf: {
      activateFormat: (id: string) => `/pdf/format/${id}/activate`,
      postFormat: '/pdf/format',
      getFormats: (p: any) => `/pdf/formats?${JSON.stringify(p)}`,
      getProductObjects: (id: string) => `/pdf/product/${id}/objects`,
      updateFormat: (id: string) => `/pdf/format/${id}`,
    },
  },
}));

jest.mock('@api/Pdf', () => ({
  __esModule: true,
  apiPatchFormat: jest.fn(),
  apiPostFormat: jest.fn(),
  apiGetFormats: jest.fn(),
}));

jest.mock('@utils/RequestError', () => ({
  __esModule: true,
  extractErrorMessage: jest.fn((e: any) => (e?.message ? e.message : String(e))),
}));

import { apiPatchFormat, apiPostFormat, apiGetFormats } from '@api/Pdf';
import { extractErrorMessage } from '@utils/RequestError';

const createRunner = (state: any = {}) => {
  const actions: any[] = [];
  const dispatch = (action: any) => {
    actions.push(action);
    if (typeof action === 'function') {
      return action(dispatch, () => state, undefined);
    }
    return action;
  };
  const getState = () => state;
  return { dispatch, getState, actions };
};

describe('pdf thunks', () => {
  afterEach(() => jest.clearAllMocks());

  it('activateFormat retorna message y despacha un thunk (getFormats) cuando data existe', async () => {
    (apiPatchFormat as jest.Mock).mockResolvedValueOnce({
      data: { id: 'f1' },
      message: ['activated'],
    });

    const { dispatch, getState, actions } = createRunner();

    const result: any = await (activateFormat('f1') as any)(dispatch, getState, undefined);

    expect(result.type).toBe('pdf/activateFormat/fulfilled');
    expect(result.payload).toBe('activated');

    // Se despachó al menos un thunk (getFormats)
    const hasThunk = actions.some(a => typeof a === 'function');
    expect(hasThunk).toBe(true);
  });

  it('createFormat fulfilled', async () => {
    (apiPostFormat as jest.Mock).mockResolvedValueOnce({ message: ['ok'] });
    const { dispatch, getState } = createRunner();
    const result: any = await (createFormat({ name: 'fmt' }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('pdf/postFormat/fulfilled');
    expect(result.payload).toEqual({ error: false, message: 'ok' });
  });

  it('createFormat rejected mapea error', async () => {
    (apiPostFormat as jest.Mock).mockRejectedValueOnce(new Error('bad'));
    const { dispatch, getState } = createRunner();
    const result: any = await (createFormat({}) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('pdf/postFormat/rejected');
    expect(extractErrorMessage).toHaveBeenCalled();
    expect(result.payload).toEqual({ error: true, message: 'bad' });
  });

  it('getFormats fulfilled', async () => {
    (apiGetFormats as jest.Mock).mockResolvedValueOnce({ data: { content: [{ id: 'a' }] } });
    const { dispatch, getState } = createRunner();
    const result: any = await (getFormats({ page: 0 }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('pdf/getFormats/fulfilled');
    expect(result.payload).toEqual({ content: [{ id: 'a' }] });
  });

  it('getProductObjects fulfilled', async () => {
    (apiGetFormats as jest.Mock).mockResolvedValueOnce({ data: [{ id: 'o1' }, { id: 'o2' }] });
    const { dispatch, getState } = createRunner();
    const result: any = await (getProductObjects('p1') as any)(dispatch, getState, undefined);
    expect(result.type).toBe('pdf/getProductObjects/fulfilled');
    expect(result.payload).toEqual([{ id: 'o1' }, { id: 'o2' }]);
  });

  it('updateFormat fulfilled', async () => {
    (apiPostFormat as jest.Mock).mockResolvedValueOnce({ message: ['upd'] });
    const { dispatch, getState } = createRunner();
    const result: any = await (updateFormat({ id: 'f1', a: 1 }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('pdf/updateFormat/fulfilled');
    expect(result.payload).toEqual({ error: false, message: 'upd' });
  });

  it('updateFormat rejected', async () => {
    (apiPostFormat as jest.Mock).mockRejectedValueOnce(new Error('oops'));
    const { dispatch, getState } = createRunner();
    const result: any = await (updateFormat({ id: 'f1' }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('pdf/updateFormat/rejected');
    expect(result.payload).toEqual({ error: true, message: 'oops' });
  });
});
