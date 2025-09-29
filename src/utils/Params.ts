import { ITEMS_PER_PAGE } from '@constants/Paginator';
import type { IParams } from '@models/Request';

export const createRequestParams = ({ page = 0, search = '' }: IParams): string => {
    return `?${page >= 0 ? 'page=' + page : ''}&size=${ITEMS_PER_PAGE}${search ? '&search=' + search : '&search='}`;
};
