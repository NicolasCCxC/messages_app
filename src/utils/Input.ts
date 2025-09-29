/**
 * validate alpha numeric structure
 *
 * @param e: React.KeyboardEvent<HTMLInputElement> - event
 * @returns void
 */
export const validatePattern = (value: string, regex: RegExp): boolean => regex.test(value);
