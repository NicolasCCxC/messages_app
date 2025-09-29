import reducer from './executionAssistedProcessSlice';
import { getAssistedProcess } from './actions';

describe('executionAssistedProcessSlice', () => {
  it('estado inicial', () => {
    const state = reducer(undefined, { type: '@@INIT' } as any);
    expect(state).toEqual({ data: {}, elements: [] });
  });

  it('getAssistedProcess.fulfilled → set data y elements desde content', () => {
    const prev = { data: {}, elements: [] };
    const payload = { content: [{ id: 'x' }, { id: 'y' }], totalPages: 9 };
    const next = reducer(prev, { type: getAssistedProcess.fulfilled.type, payload });
    expect(next.data).toEqual(payload);
    expect(next.elements).toEqual(payload.content);
  });

  it('getAssistedProcess.fulfilled sin content → elements=[]', () => {
    const prev = { data: {}, elements: [{ id: 'old' }] };
    const payload = { foo: 'bar' };
    const next = reducer(prev, { type: getAssistedProcess.fulfilled.type, payload } as any);
    expect(next.data).toEqual(payload);
    expect(next.elements).toEqual([]);
  });
});
