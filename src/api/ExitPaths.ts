import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiDeletePath = (request: FetchRequest): Promise<unknown> => FetchClient.delete(request.resource);
export const apiGetPaths = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiPostPath = (request: FetchRequest): Promise<unknown> => FetchClient.post(request.resource, request.data);
export const apiPatchPath = (request: FetchRequest): Promise<unknown> => FetchClient.patch(request.resource, request.data);
