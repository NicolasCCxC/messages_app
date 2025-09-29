import React from 'react';
import { Link } from 'react-router-dom';
import { IBreadcrumbProps } from '.';

export const Breadcrumb: React.FC<IBreadcrumbProps> = ({ items, className = '' }) => {
    return (
        <nav aria-label="Breadcrumb" className={`flex h-[0.9375rem] items-center text-sm text-muted-foreground ${className}`}>
            {items.map((item, index) => (
                <div key={item.path + index} className="flex items-center">
                    {index > 0 && <p className="mx-1 text-gray-dark text-[0.8125rem]">{'>'}</p>}
                    <Link to={item.path} className="text-gray-dark text-[0.8125rem] leading-4 hover:underline">
                        {item.title}
                    </Link>
                </div>
            ))}
        </nav>
    );
};
