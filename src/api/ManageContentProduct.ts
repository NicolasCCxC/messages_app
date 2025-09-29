import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetManageContentProduct = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

export const apiPostManageContentProduct = (request: FetchRequest): Promise<unknown> =>
    FetchClient.post(request.resource, request.data);

export const apiPatchManageContentProduct = (request: FetchRequest): Promise<unknown> =>
    FetchClient.patch(request.resource, request.data);

export const apiDeleManageContentProduct = (request: FetchRequest): Promise<unknown> => FetchClient.delete(request.resource);
