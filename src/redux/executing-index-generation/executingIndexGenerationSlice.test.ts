import reducer from './executingIndexGenerationSlice';
import { createIndex, getIndex } from './actions';

describe('executingIndexGenerationSlice', () => {
  it('estado inicial', () => {
    const state = reducer(undefined, { type: '@@INIT' } as any);
    expect(state).toEqual({ data: {}, elements: [] });
  });

  it('createIndex.fulfilled → set elements', () => {
    const prev = { data: {}, elements: [] };
    const payload = { elements: [{ id: 'e1' }, { id: 'e2' }], message: 'ok' };
    const next = reducer(prev, { type: createIndex.fulfilled.type, payload });
    expect(next.elements).toEqual(payload.elements);
    expect(next.data).toEqual({}); // no cambia
  });

  it('getIndex.fulfilled → set data y elements desde content', () => {
    const prev = { data: {}, elements: [] };
    const payload = { content: [{ id: 'c1' }], totalPages: 2 };
    const next = reducer(prev, { type: getIndex.fulfilled.type, payload });
    expect(next.data).toEqual(payload);
    expect(next.elements).toEqual([{ id: 'c1' }]);
  });

  it('getIndex.fulfilled con content ausente → elements=[]', () => {
    const prev = { data: {}, elements: [{ id: 'old' }] };
    const payload = { totalPages: 3 }; // sin content
    const next = reducer(prev, { type: getIndex.fulfilled.type, payload } as any);
    expect(next.data).toEqual(payload);
    expect(next.elements).toEqual([]);
  });
});
