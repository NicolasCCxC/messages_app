import React from 'react';
import { Icon } from '@components/icon';
import { Snackbar, Alert } from '@mui/material';
import { IToastProps, NotificationType } from '.';
import './Toast.scss';

export const Toast: React.FC<IToastProps> = ({
    open,
    message = 'Campos obligatorios',
    autoHideDuration = 5000,
    onClose,
    type,
}) => {
    return (
        <Snackbar
            className={`toast toast--${type?.toLowerCase()}`}
            open={open}
            onClose={onClose}
            autoHideDuration={autoHideDuration}
            anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
        >
            <Alert icon={<Icon name={type === NotificationType.Error ? 'exclamationRed' : 'checkCircle'} />}>{message}</Alert>
        </Snackbar>
    );
};
