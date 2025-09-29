jest.mock('@constants/Paginator', () => ({
    __esModule: true,
    ITEMS_PER_PAGE: 50,
  }));

  import { createRequestParams } from '@utils/Params';
  
  describe('createRequestParams', () => {
    it('con defaults: page=0 y search vacío', () => {
      expect(createRequestParams({} as any)).toBe('?page=0&size=50&search=');
    });
  
    it('omite page si es negativo', () => {
      expect(createRequestParams({ page: -1, search: '' } as any)).toBe('?&size=50&search=');
    });
  
    it('incluye search cuando se envía', () => {
      expect(createRequestParams({ page: 2, search: 'hola' } as any)).toBe('?page=2&size=50&search=hola');
    });
  });
  