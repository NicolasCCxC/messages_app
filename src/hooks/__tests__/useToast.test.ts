import { renderHook, act } from '@testing-library/react';
import { useToast } from '../useToast';
import { NotificationType } from '@components/toast';

const REQUIRED_FIELDS = 'Todos los campos marcados con * son obligatorios';

describe('useToast Hook', () => {
    it('should initialize with a null toast', () => {
        const { result } = renderHook(() => useToast());
        expect(result.current.toast).toBeNull();
    });

    it('should show a toast with a message and type when toggleToast is called', () => {
        const { result } = renderHook(() => useToast());
        const message = 'Operación completada exitosamente';

        act(() => {
            result.current.toggleToast(message);
        });

        expect(result.current.toast).toEqual({
            message: message,
        });
    });

    it('should hide the toast if toggleToast is called when a toast is already active', () => {
        const { result } = renderHook(() => useToast());

        act(() => {
            result.current.toggleToast('Primer mensaje');
        });
        expect(result.current.toast).not.toBeNull();

        act(() => {
            result.current.toggleToast('Segundo mensaje');
        });

        expect(result.current.toast).toBeNull();
    });

    it('should override the type to Error if the message is REQUIRED_FIELDS', () => {
        const { result } = renderHook(() => useToast());

        act(() => {
            result.current.toggleToast(REQUIRED_FIELDS, NotificationType.Error);
        });

        expect(result.current.toast).toEqual({
            message: REQUIRED_FIELDS,
            type: NotificationType.Error,
        });
    });

    it('should hide the toast when toggleToast is called with a null message', () => {
        const { result } = renderHook(() => useToast());
        
        act(() => {
            result.current.toggleToast('Un mensaje temporal', NotificationType.Error);
        });
        expect(result.current.toast).not.toBeNull();
        
        act(() => {
            result.current.toggleToast(null);
        });

        expect(result.current.toast).toBeNull();
    });
});