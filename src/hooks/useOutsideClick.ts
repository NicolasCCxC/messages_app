import { useEffect, useRef } from 'react';

/**
 * Custom hook to handle clicks outside a referenced element.
 * @param callback Function to execute when a click outside is detected.
 * @returns React ref object to attach to the element being monitored.
 */
export const useOutsideClick = (callback: () => void): React.MutableRefObject<HTMLDivElement | null> => {
    const ref = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        const handleClickOutside = (e: MouseEvent): void => {
            if (ref.current && !ref.current.contains(e.target as Node)) {
                callback();
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return (): void => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [callback]);

    return ref;
};
