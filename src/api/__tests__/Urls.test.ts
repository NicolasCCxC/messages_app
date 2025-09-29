jest.mock('@utils/Params', () => ({
    __esModule: true,
    createRequestParams: jest.fn(() => '?p=1'),
  }));
  
  import { urls } from '@api/Urls';
  import { createRequestParams } from '@utils/Params';
  
  describe('urls builders', () => {
    beforeEach(() => {
      (createRequestParams as jest.Mock).mockClear();
    });
  
    test('auth', () => {
      expect(urls.auth.login).toBe('/security/auth/login');
    });
  
    test('exitPaths', () => {
      expect(urls.exitPaths.delete('123')).toBe('/loader/path-extracts-archive-index/123');
      expect(urls.exitPaths.patch('123')).toBe('/loader/path-extracts-archive-index/123');
  
      const s1 = urls.exitPaths.get({ page: 0 } as any);
      expect(s1).toBe('/loader/path-extracts-archive-index?p=1');
      expect(createRequestParams).toHaveBeenCalledWith({ page: 0 });
    });
  
    test('pathsDataFiles', () => {
      const s1 = urls.pathsDataFiles.get({ page: 2 } as any);
      expect(s1).toBe('/loader/data-file-route?p=1');
      expect(urls.pathsDataFiles.post).toBe('/loader/data-file-route');
      expect(urls.pathsDataFiles.patch('abc')).toBe('/loader/data-file-route/abc');
    });
  
    test('pdf', () => {
      expect(urls.pdf.activateFormat('f1')).toBe('/template-admin/formats/f1');
      const s1 = urls.pdf.getFormats({ page: 3 } as any);
      expect(s1).toBe('/template-admin/formats?p=1');
      expect(urls.pdf.getProductObjects('p1')).toBe('/template-admin/objects/product/p1');
      expect(urls.pdf.postFormat).toBe('/template-admin/formats');
      expect(urls.pdf.updateFormat('u1')).toBe('/template-admin/formats?id=u1');
    });
  
    test('productInput', () => {
      expect(urls.productInput.delete('id1')).toBe('/loader/input-product-structure/id1');
      const s1 = urls.productInput.get({ q: 1 } as any);
      expect(s1).toBe('/loader/input-product-structure?p=1');
      expect(urls.productInput.patch('x')).toBe('/loader/input-product-structure/x');
      expect(urls.productInput.post).toBe('/loader/input-product-structure');
      expect(urls.productInput.getAll('PID')).toBe('/loader/input-product-structure?filterProductBy=PID');
    });
  
    test('productManagement', () => {
      const s1 = urls.productManagement.get({ page: 9 } as any);
      expect(s1).toBe('/loader/product?p=1');
      expect(urls.productManagement.getEverything).toBe('/loader/product?getAll=true');
      expect(urls.productManagement.post).toBe('/loader/product');
      expect(urls.productManagement.patch('pm1')).toBe('/loader/product/pm1');
    });
  
    test('userManagement', () => {
      const s1 = urls.userManagement.get({ page: 1 } as any);
      expect(s1).toBe('/security/user?p=1');
      expect(urls.userManagement.post).toBe('/security/user');
      expect(urls.userManagement.patch('u1')).toBe('/security/user/u1');
    });
  
    test('userRoles', () => {
      const s1 = urls.userRoles.get({ q: 'x' } as any);
      expect(s1).toBe('/security/role?p=1');
      expect(urls.userRoles.update('r1')).toBe('/security/role/r1');
    });
  
    test('manageContentProduct', () => {
      const s1 = urls.manageContentProduct.get({ page: 0 } as any);
      expect(s1).toBe('/loader/content-index-file?p=1');
      expect(urls.manageContentProduct.post).toBe('/loader/content-index-file');
      expect(urls.manageContentProduct.patch('mc1')).toBe('/loader/content-index-file/mc1');
      expect(urls.manageContentProduct.delete('mc1')).toBe('/loader/content-index-file/mc1');
    });
  
    test('objectManageFormat', () => {
      const s1 = urls.objectManageFormat.get({ page: 5 } as any);
      expect(s1).toBe('/template-admin/objects?p=1');
      expect(urls.objectManageFormat.getOne('o1')).toBe('/template-admin/objects/o1');
      expect(urls.objectManageFormat.delete('o1')).toBe('/template-admin/objects/o1');
      expect(urls.objectManageFormat.post).toBe('/template-admin/objects');
      expect(urls.objectManageFormat.patch('o2')).toBe('/template-admin/objects/o2');
    });
  
    test('executingIndexGeneration', () => {
      expect(urls.executingIndexGeneration.post).toBe('/core/index/file/generate');
      const s1 = urls.executingIndexGeneration.get({ page: 1 } as any);
      expect(s1).toBe('/core/index?p=1');
    });
  
    test('inputFileUpload', () => {
      expect(urls.inputFileUpload.cancelPost('idX')).toBe('/loader/load-files-entry/idX/cancel');
      expect(urls.inputFileUpload.post).toBe('/loader/load-files-entry/start');
      const s1 = urls.inputFileUpload.get({ page: 3 } as any);
      expect(s1).toBe('/loader/load-files-entry?p=1');
    });
  
    test('auditConsultation', () => {
      const s1 = urls.auditConsultation.get({ x: 1 } as any);
      expect(s1).toBe('/binnacle/log?p=1');
    });
  
    test('executingAssistedProcess', () => {
      expect(urls.executingAssistedProcess.post).toBe('/core/extract/generate');
      const s1 = urls.executingAssistedProcess.get({ pg: 1 } as any);
      expect(s1).toBe('/core/extract?p=1&getAllExtracts=true');
    });
  
    test('queryingHistoricalProcesses', () => {
      const s1 = urls.queryingHistoricalProcesses.get({ page: 0 } as any);
      expect(s1).toBe('/core/extract?p=1');
    });
  });
  