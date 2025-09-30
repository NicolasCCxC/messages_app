import { useContext } from 'react';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { ELEMENT_TOOLS } from '.';

export const SidebarTools: React.FC = () => {
    const { selectedElementType } = useContext(ManageObjectContext);
    const ToolToShow = ELEMENT_TOOLS[selectedElementType ?? ''];
    return (
        <div data-testid="sidebar-container" className="w-[13.25rem] px-2 h-[29.8125rem] bg-gray-light border border-gray-dark">
            {selectedElementType && (
                <>
                    <p className="text-black mt-7 mb-[1.1875rem]">Herramientas</p>
                    <ToolToShow />
                </>
            )}
        </div>
    );
};
