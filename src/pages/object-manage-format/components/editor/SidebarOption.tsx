import { useContext, useState } from 'react';
import { DialogModal, DialogModalType } from '@components/modal';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { Icon } from '@components/icon';
import { ElementType } from '@constants/ObjectsEditor';
import { ENTER } from '@components/form';
import { ISidebarOption } from '..';

export const SidebarOption: React.FC<ISidebarOption> = ({ icon, label }) => {
    const { selectedElementType, handleClickElement, element } = useContext(ManageObjectContext);
    const [showChangeObjectModal, setShowChangeObjectModal] = useState(false);

    const selectOption = selectedElementType?.toLowerCase() === icon;

    const handleSelectedObject = (): void => {
        handleClickElement(icon.toUpperCase() as ElementType);
        setShowChangeObjectModal(false);
    };

    const shouldShowChangeModal = (): boolean => {
        return (element?.content ||
            element?.image ||
            (element.body?.cells?.length ?? 0) > 0 ||
            (element?.header?.columns?.length ?? 0) > 1 ||
            (element?.style && Object.keys(element.style).length > 2)) as boolean;
    };

    const handleOptionClick = (): void => {
        if (shouldShowChangeModal()) setShowChangeObjectModal(true);
        else handleSelectedObject();
    };

    return (
        <>
            <div
                tabIndex={0}
                role="button"
                onKeyDown={e => e.key === ENTER && handleOptionClick()}
                onClick={handleOptionClick}
                className={`cursor-pointer flex flex-col justify-center items-center w-[4.5625rem] h-[4.5625rem] mb-4.5 rounded-lg shadow-default ${
                    selectOption ? 'bg-blue-dark' : 'bg-white'
                }`}
            >
                <Icon name={icon} className="pointer-events-none" />
                <p className={`text-sm ${selectOption ? 'text-white' : 'text-black'} `}>{label}</p>
            </div>
            {showChangeObjectModal && (
                <DialogModal
                    onConfirm={handleSelectedObject}
                    onClose={() => {
                        setShowChangeObjectModal(false);
                    }}
                    type={DialogModalType.ChangeObject}
                />
            )}
        </>
    );
};
