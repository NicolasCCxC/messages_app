import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetUserManagement = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

export const apiPostUserManagement = (request: FetchRequest): Promise<unknown> =>
    FetchClient.post(request.resource, request.data);

export const apiPatchUserManagement = (request: FetchRequest): Promise<unknown> =>
    FetchClient.patch(request.resource, request.data);
