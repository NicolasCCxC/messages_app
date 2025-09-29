import { useContext, useEffect, useRef } from 'react';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { IObjectElement } from '.';

export const Text: React.FC<IObjectElement> = ({ element, isPdfMode = false, isPreviewMode = false }) => {
    const { updateElementProperties, setElement } = useContext(ManageObjectContext);

    const divRef = useRef<HTMLTextAreaElement>(null);

    useEffect(() => {
        if (divRef.current) {
            const { height, width } = divRef.current.getBoundingClientRect();

            setElement({ ...element, style: { ...element.style, width, height } });
        }
    }, []);

    const handleValue = (e: React.ChangeEvent<HTMLTextAreaElement>): void => {
        updateElementProperties('content', e.target.value);
    };

    return isPdfMode ? (
        <p className="inline-block whitespace-pre" style={element.style}>
            {element.content}
        </p>
    ) : (
        <textarea
            ref={divRef}
            style={element.style}
            value={element.content}
            onChange={handleValue}
            className={`${
                isPreviewMode ? ' w-full h-full' : 'w-[40rem] h-[9.375rem]'
            }  outline-none resize-none bg-gray-light border border-[#000000] p-4`}
        />
    );
};
