import { useCallback, useContext, useState } from 'react';
import { Icon } from '@components/icon';
import { DialogModal, DialogModalType } from '@components/modal';
import { type IOption, SelectSearch } from '@components/select-search';
import { TextInput } from '@components/text-input';
import { useModal } from '@hooks/useModal';
import { DragAndDropContext, EditorContext, INITIAL_PDF_SETTINGS } from '@pages/pdf-presentation/context';
import { useAppSelector } from '@redux/store';
import { generateRandomString } from '@utils/GenerateRandomString';
import { useDataLoader } from './useDataLoader';
import { Preview, Sidebar } from '.';

export const Editor: React.FC<{ toggleEditor: () => void }> = ({ toggleEditor }) => {
    const { formatConfig, updateFormatConfig, updatePages } = useContext(EditorContext);
    const { reset: resetDragAndDrop } = useContext(DragAndDropContext).actions;

    const { allProducts } = useAppSelector(state => state.productManagement);
    const { formats } = useAppSelector(state => state.pdf);

    const [selectedProduct, setSelectedProduct] = useState<string | null>(null);

    const { activeModal, activateModal, closeModal } = useModal();

    const { productId, version } = formatConfig;

    useDataLoader(productId);

    const getNextVersion = useCallback(
        (productId: string): string => {
            const productFormats = formats.flatMap(format => (format.productId === productId ? format.version : []));
            return String(Math.max(0, ...productFormats) + 1);
        },
        [formats]
    );

    const resetEditor = useCallback(
        (productId?: string): void => {
            const updatedConfig = productId
                ? { ...INITIAL_PDF_SETTINGS, productId, version: getNextVersion(productId) }
                : INITIAL_PDF_SETTINGS;

            updateFormatConfig(updatedConfig);
            updatePages([generateRandomString()]);
        },
        [updateFormatConfig, updatePages, getNextVersion]
    );

    const exitEditor = useCallback(() => {
        toggleEditor();
        resetDragAndDrop();
        resetEditor();
    }, [resetDragAndDrop, resetEditor, toggleEditor]);

    const handleProductChange = useCallback(
        ({ value }: IOption): void => {
            const newProductId = value as string;
            if (!productId) return resetEditor(newProductId);
            activateModal(DialogModalType.ProductChange);
            setSelectedProduct(newProductId);
        },
        [activateModal, productId, resetEditor]
    );

    const changeProduct = useCallback(() => {
        if (selectedProduct) resetEditor(selectedProduct);
        closeModal();
    }, [closeModal, selectedProduct, resetEditor]);

    const modalAction = activeModal === DialogModalType.ProductChange ? changeProduct : exitEditor;

    return (
        <div className="editor">
            <div className="flex gap-1 my-4.5 items-center ml-[2.375rem]">
                <Icon name="arrowBack" onClick={() => activateModal(DialogModalType.ExitFormat)} />
                <h2 className="text-lg text-black">Contenido del Formato PDF</h2>
            </div>
            <div className="flex gap-[1.625rem] w-max mb-4.5 ml-[1.625rem]">
                <SelectSearch
                    onChangeOption={handleProductChange}
                    options={allProducts as IOption[]}
                    label="Producto"
                    value={productId}
                    wrapperClassName="w-[18.125rem]"
                />
                <TextInput
                    disabled
                    inputWrapperClassName="h-[1.5625rem]"
                    label="Versión del formato"
                    value={version}
                    wrapperClassName="w-[10.875rem]"
                />
            </div>
            <div className="flex outline outline-gray-dark outline-[0.0313rem] flex-1 bg-red-400 min-h-0">
                <Sidebar />
                <Preview toggleEditor={toggleEditor} />
            </div>
            {activeModal && <DialogModal onClose={closeModal} onConfirm={modalAction} type={activeModal as DialogModalType} />}
        </div>
    );
};
