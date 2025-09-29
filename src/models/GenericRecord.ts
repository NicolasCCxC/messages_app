/**
 * A generic interface to use when you don't have a specific interface. Replace 'any'
 *
 * @typeParam [key: string]: any - Any key and any type value
 */

export interface IGenericRecord {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    [key: string]: any;
}
