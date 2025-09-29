import { FetchRequest } from "@models/Request";
import FetchClient from "./FetchClient";

export const apiGetHistoricalProcess = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

