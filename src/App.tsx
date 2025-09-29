import React, { useEffect } from 'react';
import { Outlet, useLocation, useNavigate } from 'react-router-dom';
import { Sidebar } from '@components/sidebar';
import { PAGES_WITHOUT_PADDING } from '@constants/PagesWithoutPadding';
import Login from '@pages/login';
import { useAppSelector } from '@redux/store';
import { IGenericRecord } from '@models/GenericRecord';

const App: React.FC = () => {
    const navigate = useNavigate();
    const {
        token,
        user: { name, roles },
    } = useAppSelector(state => state.auth);
    const { pathname } = useLocation();

    const isPageWithoutPadding = PAGES_WITHOUT_PADDING.includes(pathname);

    useEffect(() => {
        if (!token) navigate('/');
    }, [navigate, token]);

    return token ? (
        <div className="flex">
            <Sidebar />
            <div className={`bg-[#ECF0F1] pt-[3rem] ${isPageWithoutPadding ? '' : 'px-[2.375rem]'}  flex flex-col w-full flex-1`}>
                {pathname === '/' ? (
                    <div>
                        <h1 className="text-3xl font-bold text-blue-dark">Bienvenido</h1>
                        <p className="pt-2 text-lg text-blue-dark">{`${name} - ${roles.map(
                            (rol: IGenericRecord) => ` ${rol.description}`
                        )}`}</p>
                    </div>
                ) : (
                    <></>
                )}

                <Outlet />
            </div>
        </div>
    ) : (
        <Login />
    );
};

export default App;
