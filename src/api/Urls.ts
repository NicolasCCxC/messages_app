import { IParams } from '@models/Request';

import { createRequestParams } from '@utils/Params';

export const urls = {
    auth: {
        login: '/security/auth/login',
    },
    exitPaths: {
        delete: (id: string): string => `/loader/path-extracts-archive-index/${id}`,
        get: (params: IParams): string => `/loader/path-extracts-archive-index${createRequestParams(params)}`,
        patch: (id: string): string => `/loader/path-extracts-archive-index/${id}`,
        post: '/loader/path-extracts-archive-index',
    },
    pathsDataFiles: {
        get: (params: IParams): string => `/loader/data-file-route${createRequestParams(params)}`,
        post: '/loader/data-file-route',
        patch: (id: string): string => `/loader/data-file-route/${id}`,
    },
    pdf: {
        activateFormat: (id: string): string => `/template-admin/formats/${id}`,
        getFormats: (params: IParams): string => `/template-admin/formats${createRequestParams(params)}`,
        getProductObjects: (id: string): string => `/template-admin/objects/product/${id}`,
        postFormat: '/template-admin/formats',
        updateFormat: (id: string): string => `/template-admin/formats?id=${id}`,
    },
    productInput: {
        delete: (id: string): string => `/loader/input-product-structure/${id}`,
        get: (params: IParams): string => `/loader/input-product-structure${createRequestParams(params)}`,
        patch: (id: string): string => `/loader/input-product-structure/${id}`,
        post: '/loader/input-product-structure',
        getAll: (id: string): string => `/loader/input-product-structure?filterProductBy=${id}`,
    },
    productManagement: {
        get: (params: IParams): string => `/loader/product${createRequestParams(params)}`,
        getEverything: '/loader/product?getAll=true',
        post: '/loader/product',
        patch: (id: string): string => `/loader/product/${id}`,
    },
    userManagement: {
        get: (params: IParams): string => `/security/user${createRequestParams(params)}`,
        post: '/security/user',
        patch: (id: string): string => `/security/user/${id}`,
    },
    userRoles: {
        get: (params: IParams): string => `/security/role${createRequestParams(params)}`,
        update: (id: string): string => `/security/role/${id}`,
    },
    manageContentProduct: {
        get: (params: IParams): string => `/loader/content-index-file${createRequestParams(params)}`,
        post: '/loader/content-index-file',
        patch: (id: string): string => `/loader/content-index-file/${id}`,
        delete: (id: string): string => `/loader/content-index-file/${id}`,
    },
    objectManageFormat: {
        get: (params: IParams): string => `/template-admin/objects${createRequestParams(params)}`,
        getOne: (id: string): string => `/template-admin/objects/${id}`,
        delete: (id: string): string => `/template-admin/objects/${id}`,
        post: '/template-admin/objects',
        patch: (id: string): string => `/template-admin/objects/${id}`,
    },
    executingIndexGeneration: {
        post: '/core/index/file/generate',
        get: (params: IParams): string => `/core/index${createRequestParams(params)}`,
    },
    inputFileUpload: {
        cancelPost: (id: string): string => `/loader/load-files-entry/${id}/cancel`,
        post: '/loader/load-files-entry/start',
        get: (params: IParams): string => `/loader/load-files-entry${createRequestParams(params)}`,
    },
    auditConsultation: {
        get: (params: IParams): string => `/binnacle/log${createRequestParams(params)}`,
    },
    executingAssistedProcess: {
        post: '/core/extract/generate',
        get: (params: IParams): string => `/core/extract${createRequestParams(params)}&getAllExtracts=true`,
        getFormat: (productId: string): string => `/template-admin/formats/last-active/${productId}`,
    },
    queryingHistoricalProcesses: {
        get: (params: IParams): string => `/core/extract${createRequestParams(params)}`,
    },
};
