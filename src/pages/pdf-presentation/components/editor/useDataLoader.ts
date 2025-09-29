import { useCallback, useEffect } from 'react';
import { getProductObjects } from '@redux/pdf/actions';
import { resetObjects } from '@redux/pdf/pdfSlice';
import { getAllInputs } from '@redux/product-input/actions';
import { resetAllInputs } from '@redux/product-input/productInputSlice';
import { useAppDispatch } from '@redux/store';

export const useDataLoader = (productId: string | null): void => {
    const dispatch = useAppDispatch();

    const initEditorData = useCallback(() => {
        dispatch(resetObjects());
        dispatch(resetAllInputs());
    }, [dispatch]);

    const loadProductData = useCallback(() => {
        if (productId) {
            void Promise.all([dispatch(getProductObjects(productId)), dispatch(getAllInputs(productId))]);
        }
    }, [dispatch, productId]);

    useEffect(() => {
        initEditorData();
    }, [initEditorData]);

    useEffect(() => {
        loadProductData();
    }, [loadProductData]);
};
