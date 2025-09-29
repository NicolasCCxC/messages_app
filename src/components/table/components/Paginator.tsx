import { useState, useContext } from 'react';
import { Icon } from '@components/icon';
import { PaginationBoundaries, START_PAGE } from '@constants/Paginator';
import { ENTER } from '@components/form';
import { generateRandomString } from '@utils/GenerateRandomString';
import { TableContext } from '../context';
import './Styles.scss';

export const Paginator: React.FC<{
    searchValue: string;
}> = ({ searchValue }) => {
    const {
        data: { pages: totalPages },
        editing: { onPageChange },
    } = useContext(TableContext);

    const [currentPage, setCurrentPage] = useState(START_PAGE);

    const handleChangePage = (changeNumber: number): void => {
        setCurrentPage(changeNumber);
        onPageChange?.(changeNumber - PaginationBoundaries.MinPage, searchValue);
    };

    const createPagination = (): JSX.Element[] => {
        const pages: JSX.Element[] = [];
        let beforePage = currentPage - PaginationBoundaries.MinPage;
        let afterPage = currentPage + PaginationBoundaries.MinPage;

        const createArrow = (direction: 'left' | 'right', targetPage: number): JSX.Element => (
            <span className="w-[1.375rem] h-[1.375rem]" key={generateRandomString()}>
                <Icon
                    className="w-[1.375rem] h-[1.375rem] mb-1"
                    name={direction === 'left' ? 'arrowLeftBlueOutline' : 'arrowRightBlueOutline'}
                    onClick={() => handleChangePage(targetPage)}
                    onKeyDown={e => e.key === ENTER && handleChangePage(targetPage)}
                />
            </span>
        );

        const createPageItem = (pageNum: number, className = ''): JSX.Element => (
            <li
                role="button"
                tabIndex={0}
                key={generateRandomString()}
                className={`numb ${className}`}
                onClick={() => handleChangePage(pageNum)}
                onKeyDown={e => e.key === ENTER && handleChangePage(pageNum)}
            >
                <span>{pageNum}</span>
            </li>
        );

        const createEllipsis = (): JSX.Element => (
            <li key={generateRandomString()} className="dots">
                <span>...</span>
            </li>
        );

        if (currentPage > PaginationBoundaries.MinPage) {
            pages.push(createArrow('left', currentPage - PaginationBoundaries.MinPage));
        }

        const showFirst = currentPage > PaginationBoundaries.ShowFirstPageThreshold;
        const showLeftDots =
            totalPages > PaginationBoundaries.LargePageThreshold && currentPage > PaginationBoundaries.ShowEllipsisThreshold;

        if (totalPages > PaginationBoundaries.ExtendedRange && showFirst) {
            pages.push(createPageItem(PaginationBoundaries.MinPage, 'first'));
            if (showLeftDots) {
                pages.push(createEllipsis());
            }
        }

        if (currentPage === totalPages) beforePage -= 2;
        else if (currentPage === totalPages - 1) beforePage -= 1;

        if (currentPage === PaginationBoundaries.MinPage) afterPage += 2;
        else if (currentPage === PaginationBoundaries.MinPage + 1) afterPage += 1;

        for (let pageNum = beforePage; pageNum <= afterPage; pageNum++) {
            if (pageNum <= 0 || pageNum > totalPages) continue;
            const isActive = currentPage === pageNum ? 'bg-blue-light !text-white' : '';
            pages.push(createPageItem(pageNum, isActive));
        }

        const showRightDots = totalPages > PaginationBoundaries.LargePageThreshold && currentPage < totalPages - 2;

        if (totalPages > PaginationBoundaries.ExtendedRange && currentPage < totalPages - 1) {
            if (showRightDots) {
                pages.push(createEllipsis());
            }
            pages.push(createPageItem(totalPages, 'last'));
        }

        if (currentPage < totalPages) {
            pages.push(createArrow('right', currentPage + PaginationBoundaries.MinPage));
        }

        return pages;
    };

    return (
        <div className="paginator">
            <span className={`text-xs text-black ${currentPage === PaginationBoundaries.MinPage ? 'mr-[1.375rem]' : ''} `}>
                Página <span className="font-bold">{currentPage}</span> de {totalPages}:
            </span>
            <ul className={`flex ${currentPage === totalPages ? 'mr-[1.375rem]' : ''}`}>{createPagination()}</ul>
        </div>
    );
};
