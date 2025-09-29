import React, { useEffect, useState, useRef } from 'react';
import { ENTER } from '@components/form';
import { getIconName } from '@utils/Icon';
import { IIconProps } from '.';

export const Icon: React.FC<IIconProps> = ({ name, className, onClick, onKeyDown, hoverIcon = name, alt }) => {
    const [iconName, setIconName] = useState<string>('');
    const [source, setSource] = useState<string>('');
    const [isLoaded, setIsLoaded] = useState<boolean>(false);
    const [isHovering, setIsHovering] = useState<boolean>(false);
    const hoverIconRef = useRef<string | null>(null);
    const originalIconRef = useRef<string | null>(null);

    const loadIcon = async (iconToLoad: string): Promise<string> => {
        try {
            const iconModule = await import(`../../assets/icons/${iconToLoad}.svg`);
            return iconModule.default;
        } catch {
            return '';
        }
    };

    useEffect(() => {
        setIconName(getIconName(name));
    }, [name]);

    useEffect(() => {
        if (!iconName) return;

        const loadIcons = async (): Promise<void> => {
            setIsLoaded(false);

            const mainSrc = await loadIcon(iconName);
            if (mainSrc) {
                setSource(mainSrc);
                originalIconRef.current = mainSrc;

                if (hoverIcon !== name) {
                    const hoverIconName = getIconName(hoverIcon);
                    const hoverSrc = await loadIcon(hoverIconName);
                    hoverIconRef.current = hoverSrc;
                } else {
                    hoverIconRef.current = mainSrc;
                }

                setIsLoaded(true);
            }
        };

        loadIcons();
    }, [iconName, hoverIcon, name]);

    const handleMouseOver = (): void => {
        if (hoverIcon !== name && hoverIconRef.current) {
            setSource(hoverIconRef.current);
            setIsHovering(true);
        }
    };

    const handleMouseLeave = (): void => {
        if (isHovering && originalIconRef.current) {
            setSource(originalIconRef.current);
            setIsHovering(false);
        }
    };

    if (!isLoaded) {
        return <div className={`icon-placeholder w-6 h-6 inline-block ${className}`} />;
    }

    return (
        <img
            tabIndex={0}
            role="button"
            src={source}
            alt={alt ?? name}
            className={`cursor-pointer ${className}`}
            onClick={onClick}
            onMouseOver={handleMouseOver}
            onFocus={handleMouseOver}
            onMouseLeave={handleMouseLeave}
            onBlur={handleMouseLeave}
            onKeyDown={e => e.key === ENTER && onKeyDown && onKeyDown(e)}
            style={{ display: source ? 'inline-block' : 'none' }}
        />
    );
};
