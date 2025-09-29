import { useEffect, useState } from 'react';
import { Breadcrumb } from '@components/breadcrumb';
import { Title } from '@components/title';
import { Toast } from '@components/toast';
import { useToast } from '@hooks/useToast';
import { getAllProducts } from '@redux/product-management/actions';
import { useAppDispatch } from '@redux/store';
import localStorage from '@utils/LocalStorage';
import { IS_EDITOR_OPEN } from '@constants/Text';
import { Editor, FormatList } from './components';
import { DragAndDropProvider, EditorProvider, ToastProvider } from './context';
import { ROUTES } from '.';

const PdfPresentation: React.FC = () => {
    const dispatch = useAppDispatch();
    const [isEditorOpen, setIsEditorOpen] = useState(false);

    const { toast, toggleToast } = useToast();

    useEffect(() => {
        dispatch(getAllProducts());
        localStorage.set(IS_EDITOR_OPEN, 'false');
    }, [dispatch]);

    const toastProps = { ...toast, open: true, onClose: (): void => toggleToast(null) };

    const toggleEditor = (): void => {
        const newState = !isEditorOpen;
        setIsEditorOpen(newState);
        localStorage.set(IS_EDITOR_OPEN, String(newState));
    };

    return (
        <div className="flex flex-col h-[calc(100vh-3rem)]">
            <Title title="Presentación en modo diseño del Formato PDF" />
            <Breadcrumb className="ml-[2.375rem]" items={ROUTES} />
            <div className="flex flex-col flex-1 min-h-0">
                <ToastProvider value={{ toast, toggleToast }}>
                    <EditorProvider>
                        <DragAndDropProvider>
                            {isEditorOpen ? <Editor toggleEditor={toggleEditor} /> : <FormatList toggleEditor={toggleEditor} />}
                        </DragAndDropProvider>
                    </EditorProvider>
                </ToastProvider>
            </div>
            {toast && <Toast {...toastProps} />}
        </div>
    );
};

export default PdfPresentation;
