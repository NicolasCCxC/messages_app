import { Modal } from '@mui/material';
import { Button } from '@components/button';
import { DIALOG_MODAL_DATA, DialogModalType, IDialogModalProps } from '.';

export const DialogModal: React.FC<IDialogModalProps> = ({ data, onClose, onConfirm, type = DialogModalType.Delete }) => {
    const { description, title, rightButtonText = 'Aceptar' } = data ?? DIALOG_MODAL_DATA[type];
    return (
        <Modal open onClose={onClose} className="flex items-center justify-center">
            <div className="bg-white p-[2.375rem] rounded-[1.125rem] w-[25.25rem]">
                <h3 className="w-full text-lg font-bold text-center text-blue-light h-[1.375rem]">{title}</h3>
                <p className="text-lg leading-5 text-center whitespace-pre-wrap text-gray-dark my-7">{description}</p>
                <div className="flex justify-between">
                    <Button text="Cerrar" color="secondary" onClick={onClose} />
                    <Button text={rightButtonText} onClick={onConfirm} />
                </div>
            </div>
        </Modal>
    );
};
