import { FetchRequest } from "@models/Request";
import FetchClient from "./FetchClient";

export const apiGetFile = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);
export const apiPostFile = (request: FetchRequest): Promise<unknown> => FetchClient.post(request.resource, request.data);
