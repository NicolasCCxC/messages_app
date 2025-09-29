import reducer, { openSidebar, toggleSidebar } from './sidebarSlice';

describe('sidebar slice', () => {
  it('estado inicial isOpen = true', () => {
    const state = reducer(undefined, { type: '@@INIT' });
    expect(state.isOpen).toBe(true);
  });

  it('openSidebar -> true', () => {
    const state = reducer({ isOpen: false }, openSidebar());
    expect(state.isOpen).toBe(true);
  });

  it('toggleSidebar alterna valor', () => {
    const s1 = reducer({ isOpen: true }, toggleSidebar());
    expect(s1.isOpen).toBe(false);
    const s2 = reducer(s1, toggleSidebar());
    expect(s2.isOpen).toBe(true);
  });
});
