import { combineReducers, Reducer } from 'redux';
import userManagement from './user-management/userManagementSlice';
import productManagement from './product-management/productManagementSlice';
import auth from './auth/authSlice';
import roles from './user-roles/userRolesSlice';
import paths from './paths/pathsSlice';
import productInput from './product-input/productInputSlice';
import pathsDataFiles from './paths-data-files/pathsDataFileSlice';
import pdf from './pdf/pdfSlice';
import manageContentProduct from './manage-content-product/manageContentProductSlice';
import objectManageFormat from './object-manage-format/objectManageFormatSlice';
import sidebar from './sidebar/sidebarSlice';
import executingIndexGeneration from './executing-index-generation/executingIndexGenerationSlice';
import inputFileUpload from './input-file-upload/inputFileUploadSlice';
import executionAssistedProcess from './execution-assisted-process/executionAssistedProcessSlice';

export const appReducer = combineReducers({
    auth,
    productManagement,
    userManagement,
    roles,
    paths,
    productInput,
    pathsDataFiles,
    manageContentProduct,
    objectManageFormat,
    pdf,
    sidebar,
    executingIndexGeneration,
    inputFileUpload,
    executionAssistedProcess
});

export const rootReducer: Reducer = (state: RootState, action) => appReducer(state, action);

// Global state from application
export type RootState = ReturnType<typeof appReducer>;
