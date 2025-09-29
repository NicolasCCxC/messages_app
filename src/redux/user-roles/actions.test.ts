/* eslint-disable @typescript-eslint/no-explicit-any */
import { getUserRoles, updateRole } from './actions';

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
    userRoles: {
      get: (p: any) => `/roles?${JSON.stringify(p)}`,
      update: (id: string) => `/roles/${id}`,
    },
  },
}));

jest.mock('@api/UserRoles', () => ({
  __esModule: true,
  apiGetUserRoles: jest.fn(),
  apiUpdateRole: jest.fn(),
}));

jest.mock('@utils/Array', () => ({
  __esModule: true,
  replaceItem: jest.fn((arr: any[], item: any) => arr.map((x: any) => (x.id === item.id ? item : x))),
}));

import { apiGetUserRoles, apiUpdateRole } from '@api/UserRoles';
import { replaceItem } from '@utils/Array';

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

describe('user-roles thunks', () => {
  afterEach(() => jest.clearAllMocks());

  it('getUserRoles -> fulfilled', async () => {
    (apiGetUserRoles as jest.Mock).mockResolvedValueOnce({ data: [{ id: 'r1' }] });
    const { dispatch, getState } = createRunner();
    const result: any = await (getUserRoles({ page: 0 }) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('roles/getRoles/fulfilled');
    expect(result.payload).toEqual([{ id: 'r1' }]);
  });

  it('getUserRoles -> rejected', async () => {
    (apiGetUserRoles as jest.Mock).mockRejectedValueOnce(new Error('bad'));
    const { dispatch, getState } = createRunner();
    const result: any = await (getUserRoles({}) as any)(dispatch, getState, undefined);
    expect(result.type).toBe('roles/getRoles/rejected');
    expect(result.payload).toBe('Error: bad');
  });

  it('updateRole -> usa replaceItem con roles.allData y retorna message', async () => {
    (apiUpdateRole as jest.Mock).mockResolvedValueOnce({
      data: { id: 'r2', name: 'Admin' },
      message: ['ok'],
    });

    const state = { roles: { allData: [{ id: 'r1' }, { id: 'r2', name: 'User' }] } };
    const { dispatch, getState } = createRunner(state);

    const result: any = await (updateRole({ id: 'r2', name: 'Admin' } as any) as any)(
      dispatch,
      getState,
      undefined
    );

    expect(result.type).toBe('roles/updateRole/fulfilled');
    expect(replaceItem).toHaveBeenCalledWith(state.roles.allData, { id: 'r2', name: 'Admin' });
    expect(result.payload).toEqual({
      data: [{ id: 'r1' }, { id: 'r2', name: 'Admin' }],
      message: 'ok',
    });
  });

  it('updateRole -> rejected retorna {data:error, message:String(error)}', async () => {
    const err = new Error('oops');
    (apiUpdateRole as jest.Mock).mockRejectedValueOnce(err);

    const { dispatch, getState } = createRunner({ roles: { allData: [] } });
    const result: any = await (updateRole({ id: 'x' } as any) as any)(dispatch, getState, undefined);

    expect(result.type).toBe('roles/updateRole/rejected');
    expect(result.payload).toEqual({ data: err, message: 'Error: oops' });
  });
});
