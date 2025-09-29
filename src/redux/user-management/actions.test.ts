/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    getUserManagement,
    createUserManagement,
    modifyUserManagement,
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
      userManagement: {
        get: (p: any) => `/users?${JSON.stringify(p)}`,
        post: '/users',
        patch: (id: string) => `/users/${id}`,
      },
    },
  }));
  
  jest.mock('@api/UsersManagement', () => ({
    __esModule: true,
    apiGetUserManagement: jest.fn(),
    apiPostUserManagement: jest.fn(),
    apiPatchUserManagement: jest.fn(),
  }));
  
  jest.mock('@utils/Array', () => ({
    __esModule: true,
    replaceItem: jest.fn((arr: any[], item: any) => arr.map((x: any) => (x.id === item.id ? item : x))),
  }));
  
  import {
    apiGetUserManagement,
    apiPostUserManagement,
    apiPatchUserManagement,
  } from '@api/UsersManagement';
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
  
  describe('user-management thunks', () => {
    afterEach(() => jest.clearAllMocks());
  
    it('getUserManagement mapea userName', async () => {
      (apiGetUserManagement as jest.Mock).mockResolvedValueOnce({
        data: {
          content: [{ id: 'u1', name: 'Ana' }, { id: 'u2', name: 'Luis' }],
          totalPages: 2,
        },
      });
  
      const { dispatch, getState } = createRunner();
      const result: any = await (getUserManagement({ page: 0 } as any) as any)(dispatch, getState, undefined);
  
      expect(result.type).toBe('user/getUserManagement/fulfilled');
      expect(result.payload).toEqual({
        content: [
          { id: 'u1', name: 'Ana', userName: 'Ana' },
          { id: 'u2', name: 'Luis', userName: 'Luis' },
        ],
        totalPages: 2,
      });
    });
  
    it('createUserManagement mapea userName en el data', async () => {
      (apiPostUserManagement as jest.Mock).mockResolvedValueOnce({
        data: { id: 'n', name: 'Pepe' },
        message: 'ok',
      });
  
      const { dispatch, getState } = createRunner();
      const result: any = await (createUserManagement({ name: 'Pepe' } as any) as any)(
        dispatch,
        getState,
        undefined
      );
  
      expect(result.type).toBe('product/createProductManagement/fulfilled');
      expect(result.payload).toEqual({
        data: { id: 'n', name: 'Pepe', userName: 'Pepe' },
        message: 'ok',
      });
    });
  
    it('modifyUserManagement usa replaceItem con users del state', async () => {
      (apiPatchUserManagement as jest.Mock).mockResolvedValueOnce({
        data: { id: 'u2', name: 'Luisa' },
        message: 'upd',
      });
  
      const state = {
        userManagement: {
          users: [{ id: 'u1', name: 'Ana' }, { id: 'u2', name: 'Luis' }],
        },
      };
  
      const { dispatch, getState } = createRunner(state);
  
      const result: any = await (modifyUserManagement({ id: 'u2', name: 'Luisa' } as any) as any)(
        dispatch,
        getState,
        undefined
      );
  
      expect(result.type).toBe('user/modifyUserManangement/fulfilled');
      expect(replaceItem).toHaveBeenCalledWith(
        state.userManagement.users,
        { id: 'u2', name: 'Luisa', userName: 'Luisa' }
      );
      expect(result.payload).toEqual({
        data: [{ id: 'u1', name: 'Ana' }, { id: 'u2', name: 'Luisa', userName: 'Luisa' }],
        message: 'upd',
      });
    });
  });
  