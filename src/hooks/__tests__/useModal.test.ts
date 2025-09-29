import { renderHook, act } from '@testing-library/react';
import { useModal } from '../useModal';

describe('useModal Hook', () => {

    it('should have a null activeModal initially', () => {
        const { result } = renderHook(() => useModal());

        expect(result.current.activeModal).toBeNull();
    });

    /**
     * Test 2: Probar la función `activateModal`.
     * Al llamar a `activateModal` con un string, el estado debe actualizarse.
     */
    it('should set the activeModal to a specific string when activateModal is called', () => {
        const { result } = renderHook(() => useModal());
        const modalName = 'profile-modal';

        act(() => {
            result.current.activateModal(modalName);
        });

        expect(result.current.activeModal).toBe(modalName);
    });

    it('should set activeModal to null when closeModal is called', () => {
        const { result } = renderHook(() => useModal());

        act(() => {
            result.current.activateModal('any-modal');
        });

        expect(result.current.activeModal).toBe('any-modal');

        act(() => {
            result.current.closeModal();
        });

        expect(result.current.activeModal).toBeNull();
    });

    it('should set activeModal to null when activateModal is called with null', () => {
        const { result } = renderHook(() => useModal());
        act(() => {
            result.current.activateModal('settings-modal');
        });
        act(() => {
            result.current.activateModal(null);
        });
        expect(result.current.activeModal).toBeNull();
    });
});