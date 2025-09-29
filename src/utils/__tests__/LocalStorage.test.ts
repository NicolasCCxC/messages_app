/** @jest-environment jsdom */
import storage from '@utils/LocalStorage';

describe('LocalStorage wrapper', () => {
  beforeEach(() => {
    window.localStorage.clear();
    jest.restoreAllMocks();
    jest.clearAllMocks();
  });

  it('set/get guardan string', () => {
    storage.set('k', 'v');
    expect(storage.get('k')).toBe('v');
  });

  it('setObject/getObject guardan y obtienen objeto', () => {
    storage.setObject('user', { id: 1, name: 'Ana' });
    expect(storage.getObject('user')).toEqual({ id: 1, name: 'Ana' });
  });

  it('clearKey llama removeItem y elimina la clave', () => {
    const removeSpy = jest.spyOn(Storage.prototype, 'removeItem');

    storage.set('k', 'v');
    expect(storage.get('k')).toBe('v');

    storage.clearKey('k');

    expect(removeSpy).toHaveBeenCalledWith('k');
    expect(storage.get('k')).toBeUndefined();
  });
});
