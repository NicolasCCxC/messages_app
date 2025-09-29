import { FetchRequest } from "@models/Request";
import FetchClient from "./FetchClient";

export const apiGetIndex = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiPostIndex = (request: FetchRequest): Promise<unknown> => FetchClient.post(request.resource, request.data);
