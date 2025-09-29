import { ReactNode, useMemo, useState } from 'react';
import { generateRandomString } from '@utils/GenerateRandomString';
import { EditorContext, INITIAL_PDF_SETTINGS, IFormatConfig } from '.';

export const EditorProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [formatConfig, setPdfSettings] = useState<IFormatConfig>(INITIAL_PDF_SETTINGS);
    const [pages, setPages] = useState<string[]>([generateRandomString()]);

    const reset = (): void => {
        setPdfSettings(INITIAL_PDF_SETTINGS);
        setPages([generateRandomString()]);
    };

    const updateFormatConfig = (settings: IFormatConfig): void => setPdfSettings(settings);

    const updatePages = (newPages: string[]): void => setPages(newPages);

    const value = useMemo(() => ({ formatConfig, pages, reset, updateFormatConfig, updatePages }), [formatConfig, pages]);

    return <EditorContext.Provider value={value}>{children}</EditorContext.Provider>;
};
