// src/redux/user-management/userManagementSlice.test.ts
import reducer from './userManagementSlice';

const types = {
  getUserManagementFulfilled: 'user/getUserManagement/fulfilled',
  getUserManagementRejected: 'user/getUserManagement/rejected',
  createUserManagementFulfilled: 'product/createProductManagement/fulfilled', // (así viene en tu código)
  createUserManagementRejected: 'product/createProductManagement/rejected',
  modifyUserManagementFulfilled: 'user/modifyUserManangement/fulfilled', // ojo el typo Manangement
};

describe('userManagementSlice', () => {
  it('estado inicial', () => {
    const s = reducer(undefined, { type: '@@INIT' } as any);
    expect(s).toEqual({
      data: {},
      users: [],
      message: '',
      error: null,
      status: 'idle',
    });
  });

  it('getUserManagement.fulfilled → set data/users y limpia error', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const payload = { content: [{ id: 'u1' }, { id: 'u2' }], totalPages: 2 };
    const next = reducer(prev, { type: types.getUserManagementFulfilled, payload });

    expect(next.status).toBe('succeeded');
    expect(next.data).toEqual(payload);
    expect(next.users).toEqual(payload.content);
    expect(next.error).toBeNull();
  });

  it('getUserManagement.rejected → set error', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const next = reducer(prev, { type: types.getUserManagementRejected, payload: 'boom' });

    expect(next.status).toBe('failed');
    expect(next.error).toBe('boom');
  });

  it('createUserManagement.fulfilled con lista >=10 → pop y unshift', () => {
    const old = Array.from({ length: 10 }, (_, i) => ({ id: `u${i}` }));
    const prev = {
      data: { content: [...old] },
      users: [...old],
      message: '',
      error: 'was-error',
      status: 'idle',
    };

    const payload = { data: { id: 'NEW' }, message: 'created' };
    const next = reducer(prev as any, { type: types.createUserManagementFulfilled, payload });

    expect(next.status).toBe('succeeded');
    expect(next.users.length).toBe(10);
    expect(next.users[0]).toEqual({ id: 'NEW' });
    expect(next.data.content[0]).toEqual({ id: 'NEW' });
    expect(next.message).toBe('created');
    expect(next.error).toBeNull();
  });

  it('createUserManagement.rejected → set error', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const next = reducer(prev, { type: types.createUserManagementRejected, payload: 'nope' });

    expect(next.status).toBe('failed');
    expect(next.error).toBe('nope');
  });

  it('modifyUserManagement.fulfilled → reemplaza users y setea message', () => {
    const prev = {
      data: {},
      users: [{ id: 'a' }, { id: 'b' }],
      message: '',
      error: null,
      status: 'idle',
    };
    const payload = { data: [{ id: 'a' }, { id: 'b', name: 'upd' }], message: 'done' };
    const next = reducer(prev as any, { type: types.modifyUserManagementFulfilled, payload });

    expect(next.users).toEqual(payload.data);
    expect(next.message).toBe('done');
    expect(next.status).toBe('succeeded');
  });
});
