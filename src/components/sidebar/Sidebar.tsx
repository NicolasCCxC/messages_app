import React, { useEffect, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { getAllProducts } from '@redux/product-management/actions';
import { Icon } from '@components/icon';
import { DialogModal, DialogModalType } from '@components/modal';
import { MENU_ITEMS } from '@constants/MenuItems';
import { IS_EDITOR_OPEN } from '@constants/Text';
import { PAGES_WITHOUT_PADDING as PAGES_WITH_VALIDATION } from '@constants/PagesWithoutPadding';
import { ENTER } from '@components/form';
import { deleteToken } from '@redux/auth/authSlice';
import { openSidebar } from '@redux/sidebar/sidebarSlice';
import { useAppDispatch, useAppSelector } from '@redux/store';
import localStorage from '@utils/LocalStorage';
import './Sidebar.scss';

export const Sidebar: React.FC = () => {
    const dispatch = useAppDispatch();
    const location = useLocation();
    const navigate = useNavigate();
    const { isOpen } = useAppSelector(state => state.sidebar);

    const [openSections, setOpenSections] = useState<{ [key: string]: boolean }>({});
    const [showModal, setShowModal] = useState(false);
    const [nextPath, setNextPath] = useState('');

    useEffect(() => {
        dispatch(openSidebar());
        dispatch(getAllProducts())
    }, [dispatch]);

    useEffect(() => {
        MENU_ITEMS.forEach(item => {
            if (item.subItems) {
                const isActive = item.subItems.some(subItem => subItem.path === location.pathname);
                if (isActive) {
                    setOpenSections(openSection => ({
                        ...openSection,
                        [item.title]: true,
                    }));
                }
            }
        });
    }, [location]);

    const logOut = (): void => {
        dispatch(deleteToken());
    };

    const toggleSection = (section: string): void => {
        setOpenSections(openSection => ({
            ...openSection,
            [section]: !openSection[section],
        }));
    };

    const confirmNavigation = (): void => {
        if (nextPath) {
            navigate(nextPath);
            setShowModal(false);
            setNextPath('');
            localStorage.set(IS_EDITOR_OPEN, 'false');
        }
    };

    const cancelNavigation = (): void => {
        setShowModal(false);
        setNextPath('');
    };

    return (
        <div className={`side-panel ${isOpen ? 'side-panel--open' : 'side-panel--closed'}`}>
            <div className="mb-1">
                <Icon name="logoAvVillas" className="ml-2 mb-7" />
                <Link to="/" className="flex items-center">
                    <Icon name="home" className="mx-2 " /> <span className="text-lg text-blue-light">Inicio</span>
                </Link>
            </div>
            <ul>
                {MENU_ITEMS.map(item => (
                    <li key={item.title}>
                        {item.subItems && (
                            <>
                                <button
                                    className={`flex h-[2.875rem] items-center rounded-[1.125rem] justify-between w-full hover:bg-gray-light ${
                                        openSections[item.title] ? 'bg-gray-light' : ''
                                    }`}
                                    onClick={() => toggleSection(item.title)}
                                >
                                    <div className="flex items-center ml-2">
                                        <Icon name={item.icon} className="mr-2" />
                                        <p className="text-lg leading-5 text-left text-blue-light">{item.title}</p>
                                    </div>
                                    <Icon
                                        name="arrowDown"
                                        className={`transition-transform mr-2 ${openSections[item.title] ? 'rotate-180' : ''}`}
                                    />
                                </button>
                                {openSections[item.title] && (
                                    <ul className="mt-[0.125rem] ml-[1.4375rem] mb-1 border-l border-l-gray-dark">
                                        {item.subItems.map(subItem => (
                                            <li key={subItem.title}>
                                                <Link
                                                    to={subItem.path}
                                                    onClick={event => {
                                                        const isEditorOpen = localStorage.get(IS_EDITOR_OPEN) === 'true';
                                                        if (
                                                            PAGES_WITH_VALIDATION.some(path => location.pathname === path) &&
                                                            location.pathname !== subItem.path &&
                                                            isEditorOpen
                                                        ) {
                                                            event.preventDefault();
                                                            setShowModal(true);
                                                            setNextPath(subItem.path);
                                                        }
                                                    }}
                                                    className={`flex min-h-[1.9375rem] mx-1 rounded-[1.125rem] mb-[0.125rem] hover:hover:bg-gray-light0 ${
                                                        location.pathname === subItem.path ? 'bg-gray-light' : ''
                                                    }`}
                                                >
                                                    <p className="text-lg my-[0.3125rem] leading-5 ml-[1.1875rem] w-[13.875rem] text-blue-light">
                                                        {subItem.title}
                                                    </p>
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </>
                        )}
                    </li>
                ))}
            </ul>
            <button
                tabIndex={0}
                className="side-panel__exit-button"
                onClick={logOut}
                onKeyDown={e => {
                    if (e.key === ENTER) logOut();
                }}
            >
                Salir
            </button>
            {showModal && (
                <DialogModal onConfirm={confirmNavigation} onClose={cancelNavigation} type={DialogModalType.ChangePage} />
            )}
        </div>
    );
};
