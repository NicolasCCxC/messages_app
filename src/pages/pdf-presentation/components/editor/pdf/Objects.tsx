import { ReactElement } from 'react';
import { ElementType as ObjectType } from '@constants/ObjectsEditor';
import type { ExtendedObjectType, IPdfObject } from '@models/Pdf';
import { Image, Shape, Table, Text, Field } from '.';

export const OBJECTS: Record<ExtendedObjectType, (props: IPdfObject) => ReactElement> = {
    [ObjectType.Image]: (props: IPdfObject) => <Image {...props} />,
    [ObjectType.Shape]: (props: IPdfObject) => <Shape {...props} />,
    [ObjectType.Table]: (props: IPdfObject) => <Table {...props} />,
    [ObjectType.Text]: (props: IPdfObject) => <Text {...props} />,
    FIELD: (props: IPdfObject) => <Field {...props} />,
};
