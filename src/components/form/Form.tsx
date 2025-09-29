import React, { KeyboardEvent } from 'react';
import { IFormProps } from '.';

export const Form: React.FC<IFormProps> = React.memo(({ className, children }) => {
    const preventSubmitOnEnter = (e: KeyboardEvent<HTMLFormElement>): void => {
        e.preventDefault();
    };

    return (
        <form autoComplete="off" role="presentation" className={className} onSubmit={preventSubmitOnEnter}>
            {children}
        </form>
    );
});
