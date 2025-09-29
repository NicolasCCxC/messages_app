// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const extractErrorMessage = (error: any): string => {
    const [message = ''] = JSON.parse(error?.message)?.message ?? [];
    return message ?? '';
};
