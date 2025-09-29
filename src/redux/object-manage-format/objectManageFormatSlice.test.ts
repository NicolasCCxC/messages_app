// src/redux/object-manage-format/objectManageFormatSlice.test.ts
import reducer, { resetElement } from './objectManageFormatSlice';

describe('objectManageFormatSlice', () => {
  it('estado inicial', () => {
    const next = reducer(undefined, { type: '@@INIT' } as any);
    expect(next).toEqual({ data: {}, elements: [], element: {}, message: '' });
  });

  it('resetElement reducer → element = {}', () => {
    const prev = { data: {}, elements: [], element: { id: 'x' }, message: '' };
    const next = reducer(prev as any, resetElement());
    expect(next.element).toEqual({});
  });

  it('getObjectManageFormat.fulfilled → set data y elements', () => {
    const prev = { data: {}, elements: [], element: {}, message: '' };
    const payload = {
      data: { totalPages: 5 },
      elements: [{ id: 'e1' }, { id: 'e2' }],
    };
    const next = reducer(prev as any, {
      type: 'objectManageFormat/getObjectManageFormat/fulfilled',
      payload,
    });
    expect(next.data).toEqual(payload.data);
    expect(next.elements).toEqual(payload.elements);
  });

  it('getOneObject.fulfilled → set element', () => {
    const prev = { data: {}, elements: [], element: {}, message: '' };
    const payload = { id: 'one', name: 'obj' };
    const next = reducer(prev as any, {
      type: 'objectManageFormat/getOneObject/fulfilled',
      payload,
    });
    expect(next.element).toEqual(payload);
  });

  it('deleteObject.fulfilled → set elements y message', () => {
    const prev = {
      data: {},
      elements: [{ id: 'a' }, { id: 'b' }],
      element: {},
      message: '',
    };
    const payload = {
      data: [{ id: 'a' }], // simulando delete
      message: 'removed',
    };
    const next = reducer(prev as any, {
      type: 'objectManageFormat/deleteObject/fulfilled',
      payload,
    });
    expect(next.elements).toEqual(payload.data);
    expect(next.message).toBe('removed');
  });
});
