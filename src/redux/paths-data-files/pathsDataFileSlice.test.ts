// src/redux/paths-data-files/pathsDataFileSlice.test.ts
import reducer from './pathsDataFileSlice';
import { getPathsDataFile, createPathDataFile, modifyPathDataFile } from './actions';

// Estado base real del slice (evita errores de shape)
const baseState = () => reducer(undefined, { type: '@@INIT' } as any) as any;

describe('pathsDataFileSlice', () => {
  it('estado inicial contiene las keys esperadas', () => {
    const state = baseState();
    expect(state).toEqual(
      expect.objectContaining({
        paths: expect.any(Array),
        // NO esperamos "pages" porque el slice no la define
      })
    );
  });

  it('getPathsDataFile.fulfilled → actualiza paths con payload.content', () => {
    const prev = { ...baseState(), paths: [{ id: 'old' }] };
    const payload = { content: [{ id: 'a' }, { id: 'b' }], totalPages: 5 }; // totalPages es ignorado por el slice

    const next = reducer(prev, { type: getPathsDataFile.fulfilled.type, payload });
    expect(next.paths).toEqual(payload.content);
    // No tocamos next.pages porque no existe en el slice
  });

  it('createPathDataFile.fulfilled → reemplaza paths por payload.content', () => {
    const prev = { ...baseState(), paths: [{ id: 'x' }] };
    const payload = { content: [{ id: 'n1' }, { id: 'n2' }], message: 'ok' };

    const next = reducer(prev, { type: createPathDataFile.fulfilled.type, payload });
    expect(next.paths).toEqual([{ id: 'n1' }, { id: 'n2' }]);
  });

  it('modifyPathDataFile.fulfilled → reemplaza paths por payload.content', () => {
    const prev = { ...baseState(), paths: [{ id: 'x' }] };
    const payload = { content: [{ id: 'm1' }], message: 'upd' };

    const next = reducer(prev, { type: modifyPathDataFile.fulfilled.type, payload });
    expect(next.paths).toEqual([{ id: 'm1' }]);
  });
});
