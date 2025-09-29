import reducer, { resetAllInputs } from './productInputSlice';
import { createInput, deleteInput, getInputs, updateInput, getAllInputs } from './actions';

describe('productInputSlice', () => {
  const makeState = () => reducer(undefined, { type: '@@INIT' });

  test('getInputs.fulfilled → setea inputs y pages', () => {
    let state = makeState();

    const payload = { content: [{ id: 'i1' }], totalPages: 5 };
    state = reducer(state, getInputs.fulfilled(payload as any, 'req', {} as any));

    expect(state.inputs).toEqual([{ id: 'i1' }]);
    expect(state.pages).toBe(5);
  });

  test('createInput.fulfilled → reemplaza inputs por payload.data', () => {
    let state = makeState();
    const newArray = [{ id: 'a' }, { id: 'b' }];
    state = reducer(
      state,
      createInput.fulfilled({ data: newArray, message: 'ok' } as any, 'req', {} as any)
    );

    expect(state.inputs).toEqual(newArray);
  });

  test('deleteInput.fulfilled → reemplaza inputs por payload.data', () => {
    let state = makeState();
    state = reducer(
      state,
      deleteInput.fulfilled({ data: [{ id: 'x' }], message: 'm' } as any, 'r', 'id1')
    );

    expect(state.inputs).toEqual([{ id: 'x' }]);
  });

  test('updateInput.fulfilled → reemplaza inputs por payload.data', () => {
    let state = makeState();
    const updated = [{ id: 'q' }];
    state = reducer(
      state,
      updateInput.fulfilled({ data: updated, message: 'done' } as any, 'r', {} as any)
    );

    expect(state.inputs).toEqual(updated);
  });

  test('getAllInputs.fulfilled → setea allInputs', () => {
    let state = makeState();
    state = reducer(
      state,
      getAllInputs.fulfilled({ content: [{ id: 'a' }] } as any, 'r', 'pid')
    );

    expect(state.allInputs).toEqual([{ id: 'a' }]);
  });

  test('resetAllInputs reducer → limpia allInputs', () => {
    let state = makeState();
    state = { ...state, allInputs: [{ id: 1 }] as any };
    state = reducer(state, resetAllInputs());

    expect(state.allInputs).toEqual([]);
  });
});
