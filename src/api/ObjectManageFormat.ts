import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetObjectManageFormat = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

export const apiPostObjectManageFormat = (request: FetchRequest): Promise<unknown> =>
    FetchClient.post(request.resource, request.data);

export const apiDeleteObjectManageFormat = (request: FetchRequest): Promise<unknown> => FetchClient.delete(request.resource);

export const apiPatchObjectManageFormat = (request: FetchRequest): Promise<unknown> =>
    FetchClient.patch(request.resource, request.data);
