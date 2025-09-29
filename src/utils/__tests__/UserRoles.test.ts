import { getUserRoles } from '@utils/UserRoles';

describe('getUserRoles', () => {
  it('devuelve ids de roles cuando existen', () => {
    const user = { roles: [{ id: 'ADMIN' }, { id: 'USER' }] } as any;
    expect(getUserRoles(user)).toEqual(['ADMIN', 'USER']);
  });

  it('devuelve [] cuando no hay roles', () => {
    expect(getUserRoles({} as any)).toEqual([]);
  });
});
