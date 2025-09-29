import type { IGenericRecord } from '@models/GenericRecord';

/**
 * Retrieves the roles of a given user.
 *
 * @param user: IGenericRecord - The user object containing role information.
 * @returns string[]
 */
export const getUserRoles = (user: IGenericRecord): string[] => {
    return user?.roles?.map((role: IGenericRecord) => role.id) ?? [];
};
