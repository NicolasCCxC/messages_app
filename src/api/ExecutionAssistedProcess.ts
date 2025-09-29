import { FetchRequest } from "@models/Request";
import FetchClient from "./FetchClient";

export const apiGetAssistedProcess = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiPostAssistedProcess = (request: FetchRequest): Promise<unknown> => FetchClient.post(request.resource, request.data);
export const apiGetFormat = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource, request.data);
