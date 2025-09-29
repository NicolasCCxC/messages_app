import { useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { Icon, IconName } from '@components/icon';
import { DialogModal } from '@components/modal';
import { IconColor } from '@constants/Icon';
import { DefaultUserRoles } from '@constants/User';
import { useRole } from '@hooks/useRole';
import { getIconVariant } from '@utils/Icon';
import { TableContext } from '../context';
import { IItem } from '.';

export const Icons: React.FC<{ item: IItem; list?: IconName[]; updateValidate: (validate: boolean) => void }> = ({
    item,
    list = ['pencilBlue', 'trashBlue'],
    updateValidate,
}) => {
    const role = useRole();
    const {
        data: { all: allData, current: data, update: updateData },
        editing: { onDeleteRow = (): void => {}, onIconClick, onUpdateRow = (): void => {} },
    } = useContext(TableContext);

    const [openDeleteModal, setOpenDeleteModal] = useState(false);
    const [listIcons, setListIcons] = useState(list);

    const { id, edit } = item;

    useEffect(() => {
        if (role === DefaultUserRoles.Administrator) return;

        if (role === DefaultUserRoles.Writing) {
            setListIcons(list.map(icon => (icon === 'trashBlue' ? getIconVariant(icon, IconColor.Disabled) : icon)));
            return;
        }
        setListIcons(list.map(icon => getIconVariant(icon, IconColor.Disabled)));
    }, [list, role]);

    const enableRowEdit = useCallback(() => {
        updateData(data.map(row => ({ ...row, activeItem: false, ...(row.id === id && { edit: true, activeItem: true }) })));
    }, [data, updateData, id]);

    const cancelRowEditing = useCallback(() => {
        const currentItem = allData?.find(row => row.id === id) ?? {};
        updateData(data.map(row => (row.id === id ? currentItem : row)));
        updateValidate(false);
    }, [allData, data, id, updateData, updateValidate]);

    const clickSaved = useCallback(() => {
        enableRowEdit();
        onUpdateRow(id);
        updateValidate(true);
    }, [enableRowEdit, id, onUpdateRow, updateValidate]);

    const toggleDeleteModal = useCallback(() => setOpenDeleteModal(prev => !prev), []);

    const deleteRow = useCallback(() => {
        onDeleteRow(id);
        toggleDeleteModal();
    }, [id, onDeleteRow, toggleDeleteModal]);

    const iconAction = useMemo(
        (): { [key: string]: () => void } => ({
            pencilBlue: enableRowEdit,
            eyeBlue: enableRowEdit,
            trashBlue: toggleDeleteModal,
        }),
        [enableRowEdit, toggleDeleteModal]
    );

    const handleIconClick = (icon: IconName, onClick: () => void): void => (onIconClick ? onIconClick(icon, item) : onClick());

    return (
        <div className={`flex h-[2.3125rem] items-center ${edit ? 'gap-1' : 'gap-2'} ml-2`}>
            {edit ? (
                <>
                    <Icon name="cancelWhite" onClick={cancelRowEditing} hoverIcon="cancelRed" />
                    <Icon name="checkBlue" onClick={clickSaved} hoverIcon="checkRed" />
                </>
            ) : (
                <>
                    {listIcons.map(icon => (
                        <Icon
                            className={icon.includes(IconColor.Disabled) ? 'pointer-events-none' : ''}
                            key={icon}
                            name={icon}
                            onClick={(): void => handleIconClick(icon, iconAction[icon])}
                            hoverIcon={getIconVariant(icon, IconColor.Hover)}
                        />
                    ))}
                </>
            )}
            {openDeleteModal && <DialogModal onClose={toggleDeleteModal} onConfirm={deleteRow} />}
        </div>
    );
};
