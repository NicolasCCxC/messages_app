import { useMemo } from 'react';
import { RootState } from '@redux/rootReducer';
import { useAppSelector } from '@redux/store';
import { getUserRoles } from '@utils/UserRoles';
import { DefaultUserRoles } from '@constants/User';

export const useRole = (): string => {
    const { user } = useAppSelector((state: RootState) => state.auth);
    const userRoles = getUserRoles(user);

    return useMemo(() => {
        if (userRoles.includes(DefaultUserRoles.Administrator)) return DefaultUserRoles.Administrator;
        if (userRoles.includes(DefaultUserRoles.Writing)) return DefaultUserRoles.Writing;
        return DefaultUserRoles.Reading;
    }, [userRoles]);
};
