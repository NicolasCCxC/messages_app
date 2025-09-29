import { getDiff } from '@utils/Diff';

type User = { id: string; name: string; age: number; meta?: { a: number } };

describe('utils: getDiff', () => {
  const original: User = { id: '1', name: 'Ana', age: 20, meta: { a: 1 } };

  it('devuelve sólo props cambiadas', () => {
    const modified: User = { ...original, name: 'Ana María', age: 20 };
    const diff = getDiff(original, modified);
    expect(diff).toEqual({ name: 'Ana María' });
  });

  it('ignora keys con ignoreKeys', () => {
    const modified: User = { ...original, age: 25 };
    const diff = getDiff(original, modified, { ignoreKeys: ['age'] });
    expect(diff).toEqual({});
  });

  it('usa customComparators por key', () => {
    const modified: User = { ...original, meta: { a: 2 } };

    const diff = getDiff(original, modified, {
      customComparators: { meta: (ov, mv) => JSON.stringify(ov) === JSON.stringify(mv) },
    });
    expect(diff).toEqual({ meta: { a: 2 } });
  });

  it('si falta original o modified, retorna {}', () => {
    expect(getDiff<User>(null as any, {} as any)).toEqual({});
    expect(getDiff<User>({} as any, undefined as any)).toEqual({});
  });
});
