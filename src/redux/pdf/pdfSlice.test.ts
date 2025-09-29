import reducer, { resetObjects } from './pdfSlice';
import { getFormats, getProductObjects } from './actions';

describe('pdfSlice', () => {
  it('estado inicial', () => {
    const state = reducer(undefined, { type: '@@INIT' } as any);
    expect(state).toEqual({ formats: [], objects: [], pages: 1 });
  });

  it('getFormats.fulfilled → guarda formats y pages', () => {
    const prev = { formats: [], objects: [], pages: 1 };
    const payload = {
      content: [{ id: 'f1' }, { id: 'f2' }],
      totalPages: 7,
    };

    const next = reducer(prev, { type: getFormats.fulfilled.type, payload });
    expect(next.formats).toEqual(payload.content);
    expect(next.pages).toBe(7);
  });

  it('getProductObjects.fulfilled → guarda objects', () => {
    const prev = { formats: [], objects: [{ id: 'old' }], pages: 1 };
    const payload = [{ id: 'o1' }, { id: 'o2' }];
    const next = reducer(prev, { type: getProductObjects.fulfilled.type, payload });
    expect(next.objects).toEqual(payload);
  });

  it('resetObjects → limpia objects', () => {
    const prev = { formats: [], objects: [{ id: 'x' }], pages: 3 };
    const next = reducer(prev, resetObjects());
    expect(next.objects).toEqual([]);
    // no toca formats/pages
    expect(next.formats).toEqual([]);
    expect(next.pages).toBe(3);
  });
});
