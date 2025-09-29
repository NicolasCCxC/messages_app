import type { AnyAction } from '@reduxjs/toolkit';

const makeAC = (type: string) => {
  const ac: any = (payload?: any) => ({ type, payload });
  ac.type = type;
  ac.toString = () => type;
  ac.match = (a: AnyAction) => a.type === type;
  return ac;
};

export const login = Object.assign(jest.fn(), {
  pending: makeAC('auth/login/pending'),
  fulfilled: makeAC('auth/login/fulfilled'),
  rejected: makeAC('auth/login/rejected'),
  typePrefix: 'auth/login',
});

// Si en algún momento exportas default en actions.ts, puedes reflejarlo:
export default { login };
