import { ExtendedObjectType, IPdfObject } from '@models/Pdf';
import { Icon } from '@components/icon';
import { FIELD } from '@constants/Pdf';
import { FieldType } from '@models/Table';
import { OBJECTS } from '../pdf';
import { IDraggableElementProps } from '.';

export const DraggableElement: React.FC<IDraggableElementProps> = ({ element, onDragStart, onRemoveItem, lastDraggedItemId }) => {
    const isActive = lastDraggedItemId === element.id;
    const isFiledOrText = element.type === FIELD || element.type === FieldType.Text;

    return (
        <button
            type="button"
            className={`cursor-pointer absolute border ${isFiledOrText ? '!z-30' : ''} ${
                isActive ? 'z-20 border-blue-900' : 'z-10 border-transparent'
            }`}
            draggable
            onDragStart={e => onDragStart(e, element)}
            style={{ left: element.x, top: element.y }}
        >
            {OBJECTS[element.type as ExtendedObjectType]({ element } as IPdfObject)}
            {isActive && <Icon className="draggable-trash" name="trashBlue" hoverIcon="trashRed" onClick={onRemoveItem} />}
        </button>
    );
};
