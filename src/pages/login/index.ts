export { default } from './Login';

/**
 * Field lengths
 */
export enum FieldLength {
    User = 320,
    Password = 18,
}

/**
 * Allowed user characters
 */
export const USER_REGEX = /^[a-zA-Z0-9.@]*$/;
