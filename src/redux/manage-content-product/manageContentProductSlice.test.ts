// src/redux/manage-content-product/manageContentProductSlice.test.ts
import reducer from './manageContentProductSlice';

describe('manageContentProductSlice', () => {
  it('estado inicial', () => {
    const next = reducer(undefined, { type: '@@INIT' } as any);
    expect(next).toEqual({ manageData: {}, content: [], message: '' });
  });

  it('getManageContentProduct.fulfilled → set manageData y content', () => {
    const prev = { manageData: {}, content: [], message: '' };
    const payload = { content: [{ id: '1' }, { id: '2' }], totalPages: 3 };
    const next = reducer(prev as any, {
      type: 'manageContentProduct/getManageContentProduct/fulfilled',
      payload,
    });
    expect(next.manageData).toBe(payload);
    expect(next.content).toEqual(payload.content);
  });

  it('createManageContentProduct.fulfilled → reemplaza content', () => {
    const prev = {
      manageData: { content: [{ id: 'x' }] },
      content: [{ id: 'x' }],
      message: '',
    };
    const payload = { content: [{ id: 'a' }, { id: 'b' }], message: 'ok' };
    const next = reducer(prev as any, {
      type: 'manageContentProduct/createManageContentProduct/fulfilled',
      payload,
    });
    expect(next.content).toEqual(payload.content);
  });

  it('modifyManageContentProduct.fulfilled → reemplaza content', () => {
    const prev = {
      manageData: {},
      content: [{ id: 'old' }],
      message: '',
    };
    const payload = { content: [{ id: 'new' }], message: 'updated' };
    const next = reducer(prev as any, {
      type: 'manageContentProduct/modifyManageContentProduct/fulfilled',
      payload,
    });
    expect(next.content).toEqual([{ id: 'new' }]);
  });

  it('deleteContentProduct.fulfilled → usa payload.data como content', () => {
    const prev = {
      manageData: {},
      content: [{ id: 'will-be-replaced' }],
      message: '',
    };
    const payload = { data: [{ id: 'after-delete' }], message: 'deleted' };
    const next = reducer(prev as any, {
      type: 'productInput/deleteContentProduct/fulfilled',
      payload,
    });
    expect(next.content).toEqual([{ id: 'after-delete' }]);
  });
});
