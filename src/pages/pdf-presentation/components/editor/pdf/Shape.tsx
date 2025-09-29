import { memo } from 'react';
import { IPdfObject } from '@models/Pdf';
import { getBorders } from '@utils/ObjectManagement';

export const Shape: React.FC<IPdfObject> = memo(({ element }) => {
    const style = { ...element.style, ...getBorders(element.style) };

    return (
        <div
            style={style}
            className="w-[36.375rem] h-[2.5625rem] bg-gray-light border border-[#000000] flex items-center justify-center overflow-hidden cursor-pointer "
        />
    );
});
