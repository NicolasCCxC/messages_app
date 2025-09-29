import React from 'react';
import { Icon } from '@components/icon';
import { IButtonProps } from '.';
import { useRole } from '@hooks/useRole';
import { DefaultUserRoles } from '@constants/User';

export const Button: React.FC<IButtonProps> = ({
    text,
    onClick,
    color = 'primary',
    isIcon,
    buttonClassName,
    type = 'button',
    disabled,
    textClassName = '',
}) => {
    const role = useRole();
    const isReadingRole = role === DefaultUserRoles.Reading && isIcon;

    const createStylesForTypeButton = (): string => {
        const defaultClassName =
            'flex justify-center items-center w-[9.375rem] text-[0.8125rem] h-[1.5625rem] text-center shadow-default rounded-[0.25rem]';

        if (isReadingRole || disabled) {
            return `${defaultClassName} bg-gray-dark pointer-events-none text-white`;
        } else if (color === 'primary') {
            return `${defaultClassName} bg-blue-light text-white hover:bg-red`;
        } else {
            return `${defaultClassName} bg-gray text-blue-light hover:text-white hover:bg-blue-light`;
        }
    };

    return (
        <button
            type={type}
            onClick={isReadingRole ? (): void => {} : onClick}
            className={`${buttonClassName} ${createStylesForTypeButton()}`}
        >
            {isIcon && <Icon name="plusWhite" className="mr-[0.625rem]" />}
            <span className={`${textClassName} ${isIcon ? 'w-[6.125rem]' : ''}`}>{text}</span>
        </button>
    );
};
