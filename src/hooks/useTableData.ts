import { useCallback, useEffect, useState } from 'react';
import { IGenericRecord } from '@models/GenericRecord';

/**
 * This describes the properties that the hook returns
 *
 * @typeParam data: IGenericRecord[] - The current table data, which may be filtered or paginated
 * @typeParam onFieldChange: (value: string, { item, row }: IGenericRecord) => void - This function is used to edit the value of each field
 * @typeParam updateData: (data: IGenericRecord[]) => void - This is used to update the data value
 */
interface IUseTableData {
    data: IGenericRecord[];
    onFieldChange: (value: string, { item, row }: IGenericRecord) => void;
    updateData: (data: IGenericRecord[]) => void;
}

/**
 * This handles the logic related to the table data
 *
 * @param allData: IGenericRecord - All table data without pagination
 * @returns IUseTableData
 */
export const useTableData = (allData: IGenericRecord[]): IUseTableData => {
    const [data, setData] = useState<IGenericRecord[]>([]);

    const mergeItem = (incoming: IGenericRecord, previous: IGenericRecord): IGenericRecord => ({
        ...previous,
        active: previous.active ?? incoming.active,
        edit: previous.activeItem ? false : previous.edit,
    });

    const generateMergedList = useCallback(
        (prevData: IGenericRecord[]): IGenericRecord[] => {
            return allData.map(item => {
                const existingItem = prevData.find(({ id }) => id === item.id) ?? item;
                return mergeItem(item, existingItem);
            });
        },
        [allData]
    );

    const validateInitialData = useCallback(() => {
        setData(generateMergedList);
    }, [generateMergedList]);

    useEffect(() => {
        validateInitialData();
    }, [validateInitialData]);

    const onFieldChange = useCallback((value: string, { item, row }: IGenericRecord) => {
        setData(data => data.map((field, index) => (index === row ? { ...field, [item.name]: value } : field)));
    }, []);

    const updateData = useCallback((data: IGenericRecord[]) => setData(data), []);
    
    return {
        data,
        onFieldChange,
        updateData,
    };
};
