import { useEffect, useState } from 'react';
import arrow from '@assets/icons/arrow-side.svg';
import { TabName } from '@constants/Pdf';
import { toggleSidebar } from '@redux/sidebar/sidebarSlice';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { FieldsPanel, FormatPanel, ObjectPanel, TABS } from '.';
import './Styles.scss';

export const Sidebar: React.FC = () => {
    const dispatch = useAppDispatch();
    const { allInputs } = useAppSelector(state => state.productInput);
    const { isOpen } = useAppSelector(state => state.sidebar);
    const sidePanel = document.querySelector('.side-panel')?.clientWidth;

    const [sidebarLeft, setSidebarLeft] = useState(0)
    const [activeTab, setActiveTab] = useState<TabName>(TabName.Format);

    useEffect(() => {
        if (sidePanel) setSidebarLeft(sidePanel);
    }, [sidePanel])
    
    const panels = {
        [TabName.Fields]: <FieldsPanel allFields={allInputs} />,
        [TabName.Format]: <FormatPanel />,
        [TabName.Objects]: <ObjectPanel />,
    };

    const handleSidebarToggle = (): void => {
        dispatch(toggleSidebar());
    };

    return (
        <aside className="sidebar">
            {panels[activeTab]}
            <div className="sidebar__tabs">
                {TABS.map(({ className, label, name }) => {
                    const isActive = name === activeTab;
                    return (
                        <button
                            className={`sidebar__tab ${className} ${isActive ? 'sidebar__tab--active' : ''}`}
                            key={name}
                            onClick={() => setActiveTab(name)}
                        >
                            {label}
                        </button>
                    );
                })}
            </div>
            <button
            style={{ left: isOpen ? sidebarLeft: '0' }}
                className='sidebar__toggle-button'
                onClick={handleSidebarToggle}
            >
                <img className="sidebar__toggle-icon" src={arrow} alt="arrow" />
            </button>
        </aside>
    );
};
