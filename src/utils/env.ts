export const getBaseUrl = (): string => {
    const fromProcess = (process as any)?.env?.VITE_BASE_URL;

    if (typeof fromProcess === 'string' && fromProcess) return fromProcess;

    try {
        const val = new Function(
            'try { return import.meta && import.meta.env && import.meta.env.VITE_BASE_URL || "" } catch(e) { return "" }'
        )();
        if (typeof val === 'string' && val) return val;
    } catch {}

    return '';
};
