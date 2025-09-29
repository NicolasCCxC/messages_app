import { useState } from 'react';
import { useAppDispatch } from '@redux/store';
import { Icon } from '@components/icon';
import { DialogModal, DialogModalType } from '@components/modal';
import { IGenericRecord } from '@models/GenericRecord';
import { cancelFile } from '@redux/input-file-upload/actions';
import { statusActive } from '.';

export const TableIcons: React.FC<IGenericRecord> = ({ item, handleMessageToast, toggleToast }) => {
    const dispatch = useAppDispatch();
    const { id, status } = item;
    const [activeModal, setActiveModal] = useState(false);

    const handleCancel = async (): Promise<void> => {
        /* eslint-disable @typescript-eslint/no-explicit-any */
        const { payload }: any = await dispatch(cancelFile(id));
        handleMessageToast(payload?.message as string);
        toggleToast();
        setActiveModal(false);
    };

    return (
        <>
            {status === statusActive && (
                <Icon name="cancelWhite" className="ml-2 w-11 h-7" onClick={() => setActiveModal(true)} />
            )}
            {activeModal && (
                <DialogModal
                    type={DialogModalType.CancelProcess}
                    onClose={() => setActiveModal(false)}
                    onConfirm={handleCancel}
                />
            )}
        </>
    );
};
