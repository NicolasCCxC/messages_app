import React from 'react';
import { Modal as ModalMUI } from '@mui/material';
import { Button } from '@components/button';
import { IModalProps } from '.';

export const Modal: React.FC<IModalProps> = ({
    open,
    onClose,
    onSave,
    title,
    children,
    modalClassName,
    noButtons = false,
    saveButtonText,
}) => {
    return (
        <ModalMUI open={open} onClose={onClose} className="flex items-center justify-center">
            <div className={`bg-white p-[2.375rem] rounded-[1.125rem] flex flex-col ${modalClassName}`}>
                <h3 className="w-full text-lg font-bold text-center text-blue-light h-[1.375rem]">{title}</h3>
                {children}
                {!noButtons && (
                    <div className="flex items-center justify-center mt-auto">
                        <Button text="Cerrar" color="secondary" onClick={onClose} buttonClassName="mr-7" />
                        {onSave && <Button text={saveButtonText ?? 'Crear'} onClick={onSave} />}
                    </div>
                )}
            </div>
        </ModalMUI>
    );
};
