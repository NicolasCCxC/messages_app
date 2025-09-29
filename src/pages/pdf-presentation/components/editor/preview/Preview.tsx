import { useContext } from 'react';
import { Button } from '@components/button';
import { Icon } from '@components/icon';
import { PAPER_DIMENSIONS, PaperSize } from '@constants/Pdf';
import { NotificationType } from '@components/toast';
import { REQUIRED_FIELDS } from '@constants/Validation';
import type { IGenericRecord } from '@models/GenericRecord';
import { DragAndDropContext, EditorContext, ToastContext } from '@pages/pdf-presentation/context';
import { createFormat, updateFormat } from '@redux/pdf/actions';
import { useAppDispatch } from '@redux/store';
import { generateRandomString } from '@utils/GenerateRandomString';
import { convertMarginsToPx, getCssMarginVars } from '@utils/Pdf';
import { DraggableElement } from './DraggableElement';
import { FIRST_PAGE_INDEX, buildPagesPayload, parseMarginsToNumber, separateElements } from '.';
import './Preview.scss';

export const Preview: React.FC<{ toggleEditor: () => void }> = ({ toggleEditor }) => {
    const dispatch = useAppDispatch();

    const { formatConfig, pages, reset: resetEditor, updatePages, updateFormatConfig } = useContext(EditorContext);
    const {
        actions: { clearPageItems, onRemoveItem, reset: resetDragAndDrop },
        handlers: { onDragStart, onDrop },
        droppedItems,
        lastDraggedItemId,
    } = useContext(DragAndDropContext);
    const { toggleToast } = useContext(ToastContext);

    const addPage = (): void => {
        updatePages([...pages, generateRandomString()]);
    };

    const buildPayload = (): IGenericRecord => {
        return {
            active: true,
            pages: buildPagesPayload(droppedItems),
            pdfConfig: {
                paperType: formatConfig.pageSize.label,
                margins: parseMarginsToNumber(formatConfig.margins),
                fontFamily: formatConfig.font.label,
                continues: formatConfig.continues,
            },
            productId: formatConfig.productId,
            id: formatConfig.id,
        };
    };

    const deletePage = (id: string): void => {
        updatePages(pages.filter(page => page !== id));
        clearPageItems(id);
    };

    const hasPageValidationErrors = (): boolean => {
        if (!Object.keys(droppedItems).length) return true;
        if (pages.some(page => !(droppedItems as IGenericRecord)[page])) return true;
        return Object.values(droppedItems).some(items => {
            const { elements, fields } = separateElements(items);
            return !elements.length || !fields.length;
        });
    };

    const pdfMargins = convertMarginsToPx(formatConfig.margins);

    const submitFormat = async (): Promise<void> => {
        if (!formatConfig.productId || hasPageValidationErrors()) return toggleToast(REQUIRED_FIELDS);
        const payload = buildPayload();
        payload?.pages?.forEach((page: IGenericRecord) => {
            page?.elements?.forEach((el: IGenericRecord) => {
                el.positionX = Math.floor(el?.positionX - pdfMargins?.left);
                el.positionY = Math.floor(el?.positionY - pdfMargins?.top);
            });

            page?.fields?.forEach((field: IGenericRecord) => {
                field.positionX = Math.floor(field?.positionX - pdfMargins?.left);
                field.positionY = Math.floor(field?.positionY - pdfMargins?.top);
            });
        });
        const { payload: response }: IGenericRecord = await dispatch(
            formatConfig.isNew ? createFormat(payload) : updateFormat(payload)
        );
        toggleToast(response.message, response.error ? NotificationType.Error : undefined);
        resetAll();
    };

    const resetAll = (): void => {
        toggleEditor();
        resetEditor();
        resetDragAndDrop();
    };

    const pageStyle = {
        ...getCssMarginVars(pdfMargins),
        ...PAPER_DIMENSIONS[(formatConfig.pageSize.value as PaperSize) ?? PaperSize.Letter],
    };

    return (
        <div className="preview">
            <div className={`preview__pages font-${formatConfig.font.value}`}>
                {pages.map((page, index) => {
                    const items = droppedItems?.[page] ?? [];
                    return (
                        <>
                            {index === 1 && (
                                <button
                                    onClick={() => {
                                        updateFormatConfig({ ...formatConfig, continues: !formatConfig.continues });
                                    }}
                                    className="flex justify-center items-center rounded-lg !m-0 relative -bottom-7 text-white bg-blue-light w-[9.375rem] min-h-[1.875rem]"
                                >
                                    <p
                                        className={`relative w-[1.125rem] h-[1.125rem] border-[0.125rem] border-white rounded-full mr-2 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:w-[0.5rem] before:h-[0.5rem] before:rounded-full ${
                                            formatConfig.continues ? 'before:bg-white' : ''
                                        }`}
                                    />
                                    <span>Continuación</span>
                                </button>
                            )}
                            <div
                                tabIndex={0}
                                role="button"
                                key={page}
                                className="preview__page"
                                style={pageStyle}
                                onDragOver={e => e.preventDefault()}
                                onDrop={e => onDrop(e, { margins: pdfMargins, pageId: page })}
                            >
                                {items.map(item => (
                                    <DraggableElement
                                        key={item.id}
                                        element={item}
                                        onDragStart={e => onDragStart(e, item)}
                                        onRemoveItem={(): void => onRemoveItem(page, item.id)}
                                        lastDraggedItemId={lastDraggedItemId}
                                    />
                                ))}
                                {index > FIRST_PAGE_INDEX && (
                                    <Icon
                                        className="preview__trash"
                                        hoverIcon="trashRed"
                                        name="trashBlue"
                                        onClick={(): void => deletePage(page)}
                                    />
                                )}
                            </div>
                        </>
                    );
                })}
                <Button buttonClassName="min-h-[1.75rem] w-[9.25rem] text-xs" onClick={addPage} text="+Agregar página" />
            </div>
            <Button buttonClassName="preview__button--create" onClick={submitFormat} text="Crear formato" />
        </div>
    );
};
