import { useCallback, useState } from 'react';

/**
 * Return values of the useModal hook
 *
 * @property activeModal: string | null - Currently active modal or null if none is active
 * @property activateModal: (modal: string | null) => void - Activates a specific modal or closes the current modal if null
 * @property closeModal: () => void - Closes the active modal
 */
export interface IModalHook {
    activeModal: string | null;
    activateModal: (modal: string | null) => void;
    closeModal: () => void;
}

/**
 * Hook that provides the state and function to toggle a modal
 *
 * @returns IModalHook
 */
export const useModal = (): IModalHook => {
    const [activeModal, setActiveModal] = useState<string | null>(null);

    const activateModal = useCallback((modal: string | null): void => {
        setActiveModal(modal);
    }, []);

    const closeModal = useCallback((): void => {
        setActiveModal(null);
    }, []);

    return {
        activeModal,
        activateModal,
        closeModal,
    };
};
