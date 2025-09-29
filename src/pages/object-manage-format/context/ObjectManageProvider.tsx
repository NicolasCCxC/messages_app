import { ReactNode, useCallback, useMemo, useState } from 'react';
import { ElementType, ObjectType } from '@constants/ObjectsEditor';
import { ELEMENT_TYPE_TO_OBJECT_TYPE_MAP, IElement, initialElementState, ManageObjectContext } from './index';

export const ObjectManageProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [selectedElementType, setSelectedElementType] = useState<ElementType | null>(null);
    const [element, setElement] = useState<IElement>(initialElementState);

    const handleTypeElement = useCallback((elementType: string): void => {
        setElement(prev => ({
            productId: prev.productId,
            name: prev.name,
            identifier: prev.identifier,
            objectType: ELEMENT_TYPE_TO_OBJECT_TYPE_MAP[elementType] || ObjectType.Generic,
            type: elementType.toUpperCase(),
        }));
    }, []);

    const updateElementProperties = useCallback(
        (name: Exclude<keyof IElement, 'style'>, value: string | number): void => {
            setElement({ ...element, [name]: value });
        },
        [element]
    );

    const updateElementStyles = useCallback(
        (name: keyof React.CSSProperties, value: string | number): void => {
            setElement({
                ...element,
                style: {
                    ...element.style,
                    [name]: value,
                },
            });
        },
        [element]
    );

    const handleClickElement = useCallback(
        (elementType: ElementType): void => {
            setSelectedElementType(elementType);
            handleTypeElement(elementType);
        },
        [handleTypeElement]
    );

    const value = useMemo(
        () => ({
            selectedElementType,
            handleClickElement,
            element,
            updateElementProperties,
            updateElementStyles,
            setElement,
            setSelectedElementType,
        }),
        [element, handleClickElement, selectedElementType, updateElementProperties, updateElementStyles]
    );

    return <ManageObjectContext.Provider value={value}>{children}</ManageObjectContext.Provider>;
};
