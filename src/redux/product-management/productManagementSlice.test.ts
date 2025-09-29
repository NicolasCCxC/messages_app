// src/redux/product-management/productManagementSlice.test.ts
import reducer from './productManagementSlice';

const types = {
  getProductManagementFulfilled: 'product/getProductManagement/fulfilled',
  getProductManagementRejected: 'product/getProductManagement/rejected',
  getAllProductsFulfilled: 'product/getAllProducts/fulfilled',
  createProductManagementFulfilled: 'product/createProductManagement/fulfilled',
  createProductManagementRejected: 'product/createProductManagement/rejected',
  modifyProductManagementFulfilled: 'product/modifyProductManagement/fulfilled',
  modifyProductManagementRejected: 'product/modifyProductManagement/rejected',
};

describe('productManagementSlice', () => {
  it('estado inicial', () => {
    const s = reducer(undefined, { type: '@@INIT' } as any);
    expect(s).toEqual({
      data: {},
      products: [],
      error: null,
      status: 'idle',
      message: '',
      allProducts: [],
    });
  });

  it('getProductManagement.fulfilled → set data y products', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const payload = { content: [{ id: 'p1' }], page: 0, totalPages: 3 };
    const next = reducer(prev, { type: types.getProductManagementFulfilled, payload });

    expect(next.status).toBe('succeeded');
    expect(next.data).toEqual(payload);
    expect(next.products).toEqual(payload.content);
  });

  it('getProductManagement.rejected → set error', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const next = reducer(prev, { type: types.getProductManagementRejected, payload: 'boom' });

    expect(next.status).toBe('failed');
    expect(next.error).toBe('boom');
  });

  it('getAllProducts.fulfilled → set allProducts', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const payload = [{ value: 1, label: 'x' }];
    const next = reducer(prev, { type: types.getAllProductsFulfilled, payload });

    expect(next.allProducts).toEqual(payload);
  });

  it('createProductManagement.fulfilled con lista >=10 → hace pop y agrega primero', () => {
    const old = Array.from({ length: 10 }, (_, i) => ({ id: `p${i}` }));
    const prev = {
      data: { content: [...old] },
      products: [...old],
      error: null,
      status: 'idle',
      message: '',
      allProducts: [],
    };

    const payload = { data: { id: 'NEW' }, message: 'ok' };
    const next = reducer(prev as any, { type: types.createProductManagementFulfilled, payload });

    // products: pop del último + unshift del nuevo → tamaño sigue 10
    expect(next.products.length).toBe(10);
    expect(next.products[0]).toEqual({ id: 'NEW' });

    // data.content también antepone
    expect(next.data.content[0]).toEqual({ id: 'NEW' });

    expect(next.message).toBe('ok');
    expect(next.status).toBe('succeeded');
    expect(next.error).toBeNull();
  });

  it('createProductManagement.rejected → set error', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const next = reducer(prev, { type: types.createProductManagementRejected, payload: 'nope' });

    expect(next.status).toBe('failed');
    expect(next.error).toBe('nope');
  });

  it('modifyProductManagement.fulfilled → reemplaza producto por id y setea message', () => {
    const prev = {
      data: { content: [] },
      products: [{ id: 'a' }, { id: 'b' }],
      error: null,
      status: 'idle',
      message: '',
      allProducts: [],
    };
    const payload = { data: { id: 'b', name: 'updated' }, message: 'done' };
    const next = reducer(prev as any, { type: types.modifyProductManagementFulfilled, payload });

    expect(next.products).toEqual([{ id: 'a' }, { id: 'b', name: 'updated' }]);
    expect(next.message).toBe('done');
    expect(next.status).toBe('succeeded');
  });

  it('modifyProductManagement.rejected → set error', () => {
    const prev = reducer(undefined, { type: '@@INIT' } as any);
    const next = reducer(prev, { type: types.modifyProductManagementRejected, payload: 'err' });

    expect(next.status).toBe('failed');
    expect(next.error).toBe('err');
  });
});
