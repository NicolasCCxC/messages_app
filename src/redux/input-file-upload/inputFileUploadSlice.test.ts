// src/redux/input-file-upload/inputFileUploadSlice.test.ts
import reducer from './inputFileUploadSlice';

describe('inputFileUploadSlice', () => {
  it('estado inicial', () => {
    const next = reducer(undefined, { type: '@@INIT' } as any);
    expect(next).toEqual({ data: {}, elements: [] });
  });

  it('createFile.fulfilled → set elements', () => {
    const prev = { data: {}, elements: [{ id: 'old' }] };
    const payload = { elements: [{ id: 'e1' }, { id: 'e2' }] };
    const next = reducer(prev as any, {
      type: 'inputFileUpload/createFile/fulfilled',
      payload,
    });
    expect(next.elements).toEqual(payload.elements);
  });

  it('getFile.fulfilled → set data y elements (content)', () => {
    const prev = { data: {}, elements: [] };
    const payload = {
      data: { totalPages: 2 },
      content: [{ id: 'r1' }, { id: 'r2' }],
    };
    const next = reducer(prev as any, {
      type: 'inputFileUpload/getFile/fulfilled',
      payload,
    });
    expect(next.data).toEqual(payload.data);
    expect(next.elements).toEqual(payload.content);
  });
});
