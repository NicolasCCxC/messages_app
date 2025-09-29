import { useContext } from 'react';
import { ElementType as ObjectType, OBJECT_ICONS } from '@constants/ObjectsEditor';
import type { IGenericRecord } from '@models/GenericRecord';
import { DragAndDropContext } from '@pages/pdf-presentation/context';
import { useAppSelector } from '@redux/store';

export const ObjectPanel: React.FC = () => {
    const { onDragStart, onDragEnd } = useContext(DragAndDropContext).handlers;

    const { objects } = useAppSelector(state => state.pdf);

    return (
        <div className="object-panel">
            <h3 className="object-panel__title">Objetos</h3>
            <div className="object-panel__cards">
                {objects.map((item: IGenericRecord) => {
                    const { name, type } = item;
                    return (
                        <figure
                            aria-label={type}
                            className="object-panel__card"
                            draggable
                            key={item.id}
                            onDragStart={event => onDragStart(event, item)}
                            onDragEnd={onDragEnd}
                        >
                            <img alt="Object" src={OBJECT_ICONS[type as ObjectType]} />
                            <figcaption className="object-panel__card-caption" title={name}>
                                {name}
                            </figcaption>
                        </figure>
                    );
                })}
            </div>
        </div>
    );
};
