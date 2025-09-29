import { useContext } from 'react';
import { Icon } from '@components/icon';
import { DialogModal, DialogModalType } from '@components/modal';
import { useModal } from '@hooks/useModal';
import type { IGenericRecord } from '@models/GenericRecord';
import { DragAndDropContext, EditorContext, ToastContext } from '@pages/pdf-presentation/context';
import { activateFormat } from '@redux/pdf/actions';
import { useAppDispatch } from '@redux/store';
import { convertMarginsToPx } from '@utils/Pdf';
import { ITableIconsProps, buildFormatConfig, formatPageData } from '.';

export const TableIcons: React.FC<ITableIconsProps> = ({ item, toggleEditor }) => {
    const dispatch = useAppDispatch();
    const { updateDroppedItems } = useContext(DragAndDropContext).actions;
    const { updateFormatConfig, updatePages } = useContext(EditorContext);
    const { toggleToast } = useContext(ToastContext);
    const { activeModal, activateModal, closeModal } = useModal();

    const { active: isActive, id, pages: formatPages } = item;

    const onActivateClick = async (): Promise<void> => {
        if (!isActive) return activateModal(DialogModalType.Reactivation);
        handleActivateFormat();
    };

    const handleActivateFormat = async (): Promise<void> => {
        const { payload }: IGenericRecord = await dispatch(activateFormat(id));
        toggleToast(payload);
        closeModal();
    };

    const openFormatEditor = (): void => {
        const pdfMargins = convertMarginsToPx(item.pdfConfig.margins);
        const { elements, pages } = formatPageData(formatPages, pdfMargins);
        updateFormatConfig(buildFormatConfig(item));
        updateDroppedItems(elements);
        updatePages(pages);
        toggleEditor();
    };

    const activateIcon = isActive ? 'activateLocked' : 'activateBlue';
    const hoverIcon = isActive ? 'activateLocked' : 'activateRed';

    return (
        <>
            <div className={`flex h-[2.3125rem] items-center gap-2 ml-2`}>
                <Icon name="pencilBlue" hoverIcon="pencilRed" onClick={openFormatEditor} />
                {/*<Icon name="eyeBlue" hoverIcon="eyeRed" onClick={toggleVisualization} />*/}
                <Icon name={activateIcon} hoverIcon={hoverIcon} onClick={onActivateClick} />
            </div>
            {activeModal && (
                <DialogModal onClose={closeModal} onConfirm={handleActivateFormat} type={activeModal as DialogModalType} />
            )}
        </>
    );
};
