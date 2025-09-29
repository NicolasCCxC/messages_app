import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetProductManagement = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

export const apiPostProductManagement = (request: FetchRequest): Promise<unknown> =>
    FetchClient.post(request.resource, request.data);

export const apiPatchProductManagement = (request: FetchRequest): Promise<unknown> =>
    FetchClient.patch(request.resource, request.data);
