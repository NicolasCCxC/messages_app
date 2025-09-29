import { FetchRequest } from '@models/Request';
import FetchClient from './FetchClient';

export const apiGetUserRoles = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiUpdateRole = (request: FetchRequest): Promise<unknown> => FetchClient.patch(request.resource, request.data);
