import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetPathsDataFiles = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

export const apiPostPathsDataFiles = (request: FetchRequest): Promise<unknown> =>
    FetchClient.post(request.resource, request.data);

export const apiPatchPathsDataFiles = (request: FetchRequest): Promise<unknown> =>
    FetchClient.patch(request.resource, request.data);
