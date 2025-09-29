import { memo } from 'react';
import { IPdfObject } from '@models/Pdf';

export const Text: React.FC<IPdfObject> = memo(({ element }) => (
    <p className="cursor-pointer inline-block text-wrap whitespace-pre max-w-[37.5rem]" style={element.style}>
        {element.content}
    </p>
));
