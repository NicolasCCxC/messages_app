import { FetchRequest } from "@models/Request";
import FetchClient from "./FetchClient";

export const apiGetAuditConsultation = (request: FetchRequest): Promise<unknown> => FetchClient.get(request.resource);

