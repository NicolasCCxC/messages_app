import { useContext, useEffect, useRef } from 'react';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { IObjectElement, DEFAULT_IMAGE_SIZE } from '.';

export const Image: React.FC<IObjectElement> = ({ element, isPreviewMode }) => {
    const { setElement } = useContext(ManageObjectContext);

    const imgRef = useRef<HTMLImageElement>(null);

    useEffect(() => {
        setElement({ ...element, style: { ...element.style, ...DEFAULT_IMAGE_SIZE } });
    }, []);

    return (
        <>
            {element.image ? (
                <img
                    ref={imgRef}
                    src={element.image}
                    alt="Uploaded"
                    style={element.style}
                    className={isPreviewMode ? '!w-full !h-full' : ''}
                />
            ) : (
                <div
                    className={`${
                        isPreviewMode ? '!w-full !h-full' : ''
                    } w-[33.1875rem] h-[9.375rem] bg-gray-light border border-[#000000] flex items-center justify-center overflow-hidden`}
                />
            )}
        </>
    );
};
