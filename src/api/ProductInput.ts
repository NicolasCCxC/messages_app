import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiDeleteInput = (request: FetchRequest): Promise<unknown> => FetchClient.delete(request.resource);
export const apiGetInputs = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiPostInput = (request: FetchRequest): Promise<unknown> => FetchClient.post(request.resource, request.data);
export const apiPatchInput = (request: FetchRequest): Promise<unknown> => FetchClient.patch(request.resource, request.data);
