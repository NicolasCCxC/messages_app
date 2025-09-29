import React from 'react';
import { ITitleProps } from '.';

export const Title: React.FC<ITitleProps> = ({ title, className }) => {
    return (
        <h1
            className={`text-[1.8125rem] h-[2.1875rem] text-blue-dark flex w-full justify-center font-bold font-roboto mb-4.5 ${className}`}
        >
            {title}
        </h1>
    );
};
