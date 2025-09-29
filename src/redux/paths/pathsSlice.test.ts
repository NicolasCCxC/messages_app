import reducer from './pathsSlice';
import { createPath, deletePath, getExitPaths, updatePath } from './actions';

describe('pathsSlice', () => {
  it('estado inicial', () => {
    const state = reducer(undefined, { type: '@@INIT' } as any);
    expect(state).toEqual({ paths: [], pages: 1 });
  });

  it('getExitPaths.fulfilled → set paths/pages', () => {
    const prev = { paths: [], pages: 1 };
    const payload = { content: [{ id: 'p1' }], totalPages: 4 };
    const next = reducer(prev, { type: getExitPaths.fulfilled.type, payload });
    expect(next.paths).toEqual([{ id: 'p1' }]);
    expect(next.pages).toBe(4);
  });

  it('createPath.fulfilled → reemplaza paths por payload.data', () => {
    const prev = { paths: [{ id: 'a' }], pages: 5 };
    const payload = { data: [{ id: 'nuevo' }, { id: 'otro' }], message: 'ok' };
    const next = reducer(prev, { type: createPath.fulfilled.type, payload });
    expect(next.paths).toEqual(payload.data);
    expect(next.pages).toBe(5); // no cambia
  });

  it('updatePath.fulfilled → reemplaza paths por payload.data', () => {
    const prev = { paths: [{ id: 'a' }], pages: 2 };
    const payload = { data: [{ id: 'a' }, { id: 'b' }], message: 'upd' };
    const next = reducer(prev, { type: updatePath.fulfilled.type, payload });
    expect(next.paths).toEqual(payload.data);
  });

  it('deletePath.fulfilled → reemplaza paths por payload.data', () => {
    const prev = { paths: [{ id: 'a' }, { id: 'b' }], pages: 2 };
    const payload = { data: [{ id: 'b' }], message: 'del' };
    const next = reducer(prev, { type: deletePath.fulfilled.type, payload });
    expect(next.paths).toEqual([{ id: 'b' }]);
  });
});
