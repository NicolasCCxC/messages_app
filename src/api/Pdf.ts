import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetFormats = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiGetProductObject = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiPostFormat = (request: FetchRequest): Promise<unknown> => FetchClient.post(request.resource, request.data);
export const apiPatchFormat = (request: FetchRequest): Promise<unknown> => FetchClient.patch(request.resource, request.data);
