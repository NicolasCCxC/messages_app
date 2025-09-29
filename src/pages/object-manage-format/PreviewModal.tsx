import React, { useMemo } from 'react';
import { useAppSelector } from '@redux/store';
import { Modal } from '@components/modal';
import { elements } from './components';
import { IElement } from './context';

export const PreviewModal: React.FC<{ togglePreviewModal: () => void }> = ({ togglePreviewModal }) => {
    const { element } = useAppSelector(state => state.objectManageFormat);
    const ElementToToShow = useMemo(() =>elements[element.type], [element.type]);

    return (
        <Modal modalClassName="w-[30.375rem] h-[23.6875rem]" title="Visualizar" open onClose={togglePreviewModal}>
            <div className="w-[25.625rem] h-[12.5rem] bg-gray mt-7">
                {ElementToToShow && (<ElementToToShow isPreviewMode element={element as IElement} />) }
            </div>
        </Modal>
    );
};
