import { memo } from 'react';
import { IPdfObject } from '@models/Pdf';

export const Image: React.FC<IPdfObject> = memo(({ element }) => (
    <img className="cursor-pointer" src={element.image} alt="Uploaded" style={element.style} />
));
