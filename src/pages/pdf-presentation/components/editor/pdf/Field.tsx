import { memo } from 'react';
import { IPdfObject } from '@models/Pdf';
import { IGenericRecord } from '@models/GenericRecord';

export const Field: React.FC<IPdfObject> = memo(({ element }) => (
    <p className="text-xs cursor-pointer text-blue">{(element as IGenericRecord)?.fieldName}</p>
));
