import { SidebarOption } from './SidebarOption';
import { sidebarOptions } from '..';

export const Sidebar: React.FC = () => {
    return (
        <div className="w-[7rem] pt-7 h-[29.8125rem] bg-gray-light border border-gray-dark">
            <p className="text-base ml-2 leading-5 mb-[1.875rem] text-gray-dark w-[4.5625rem]">Crear objetos</p>
            <div className="ml-4">
                {sidebarOptions.map(sidebarOption => (
                    <SidebarOption key={sidebarOption.label} {...sidebarOption} />
                ))}
            </div>
        </div>
    );
};
