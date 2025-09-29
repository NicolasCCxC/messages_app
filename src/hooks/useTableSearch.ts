import { useState } from 'react';
import { ChangeEvent } from '@models/Input';

/**
 * This describes the properties that the hook returns
 *
 * @typeParam displaySearchMessage: () => void - This is used to activateBlue the flag that indicates when to display the search message
 * @typeParam handleSearchChange: (e: ChangeEvent) => void - This is used to edit the search value
 * @typeParam searchValue: string - This is the search value
 * @typeParam showSearchMessage: boolean - This indicates whether the search message should be displayed
 */
interface IUseTableSearch {
    displaySearchMessage: () => void;
    handleSearchChange: (e: ChangeEvent) => void;
    searchValue: string;
    showSearchMessage: boolean;
}

/**
 * This handles the logic related to searching for data in the table
 *
 * @returns IUseTableSearch
 */
export const useTableSearch = (): IUseTableSearch => {
    const [searchValue, setSearchValue] = useState('');
    const [showSearchMessage, setShowSearchMessage] = useState(false);

    const handleSearchChange = ({ target: { value } }: ChangeEvent): void => setSearchValue(value);

    const displaySearchMessage = (): void => setShowSearchMessage(true);

    return {
        displaySearchMessage,
        handleSearchChange,
        searchValue,
        showSearchMessage,
    };
};
