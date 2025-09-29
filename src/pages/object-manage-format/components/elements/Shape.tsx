import { useContext, useEffect, useRef } from 'react';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { getBorders } from '@utils/ObjectManagement';
import { IObjectElement } from '.';

export const Shape: React.FC<IObjectElement> = ({ element, isPreviewMode }) => {
    const { setElement } = useContext(ManageObjectContext);

    const divRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (divRef.current) {
            const { height, width } = divRef.current.getBoundingClientRect();
            setElement({ ...element, style: { ...element.style, width, height } });
        }
    }, []);

    const style = { ...element.style, ...getBorders(element.style) };

    return (
        <div
            ref={divRef}
            style={style}
            className={`${isPreviewMode ? '!w-full !h-full' :''} w-[36.375rem] h-[2.5625rem] bg-gray-light border border-[#000000] flex items-center justify-center overflow-hidden`}
        />
    );
};
