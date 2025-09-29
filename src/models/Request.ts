import { IGenericRecord } from './GenericRecord';

export enum HttpMethod {
    POST = 'POST',
    GET = 'GET',
    DELETE = 'DELETE',
    PUT = 'PUT',
    PATCH = 'PATCH',
}

export class FetchRequest {
    resource: string;
    data?: IGenericRecord[] | IGenericRecord;
    start_date?: number;
    finish_date?: number;

    /**
     * Constructor
     * @param resource: string - Service resource
     * @param data: IGenericRecord[] | IGenericRecord - Optional data request
     * @param start_date: number - Optional start date request
     * @param finish_date: number - Optional finish date request
     */
    constructor(resource: string, data?: IGenericRecord[] | IGenericRecord, start_date?: number, finish_date?: number) {
        this.resource = resource;
        this.data = data;
        this.start_date = start_date;
        this.finish_date = finish_date;
    }
}

/**
 * Interface for pagination and search parameters.
 *
 * @typeParam page: number - Optional current page number for pagination.
 * @typeParam size: number - Optional number of items per page.
 * @typeParam search: string - Optional search query used for filtering results.
 */
export interface IParams {
    page?: number;
    size?: number;
    search?: string;
}
