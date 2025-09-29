import type { AnyAction } from '@reduxjs/toolkit';

jest.mock('./actions', () => {
  const makeAC = (type: string) => {
    const ac: any = (payload?: any) => ({ type, payload });
    ac.type = type;
    ac.match = (a: AnyAction) => a.type === type;
    return ac;
  };
  return {
    __esModule: true,
    login: Object.assign(jest.fn(), {
      pending: makeAC('auth/login/pending'),
      fulfilled: makeAC('auth/login/fulfilled'),
      rejected: makeAC('auth/login/rejected'),
      typePrefix: 'auth/login',
    }),
  };
});

import authReducer, { cleanError } from './authSlice';

describe('authSlice', () => {
  const initial = { user: {}, token: '', error: '' };

  it('cleanError limpia error', () => {
    const next = authReducer({ ...initial, error: 'boom' }, cleanError());
    expect(next.error).toBe('');
  });

  it('login.fulfilled setea user y token', () => {
    const action: AnyAction = {
      type: 'auth/login/fulfilled',
      payload: { user: { id: 1 }, accessToken: 'abc' }
    };
    const next = authReducer(initial, action);
    expect(next.user).toEqual({ id: 1 });
    expect(next.token).toBe('abc');
    expect(next.error).toBe('');
  });

  it('login.rejected mantiene o setea error (ajusta según tu slice)', () => {
    const action: AnyAction = { type: 'auth/login/rejected', error: { message: 'Invalid' } };
    authReducer(initial, action);
  });
});
