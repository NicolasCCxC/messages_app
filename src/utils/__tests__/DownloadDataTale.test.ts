import { handleExportCSV, handleExportPDF } from '../DownloadDataTale';
import { IGenericRecord } from '@models/GenericRecord';
import { IFields } from '@models/Table';

describe('Data Export Utilities', () => {

  describe('handleExportCSV', () => {
    const mockLink = {
      href: '',
      download: '',
      click: jest.fn(),
    };

    beforeEach(() => {
      jest.clearAllMocks();
      
      global.URL.createObjectURL = jest.fn(() => 'mock-url');
      document.createElement = jest.fn(() => mockLink as any);
      document.body.appendChild = jest.fn();
      document.body.removeChild = jest.fn();
    });

    it('debería generar un CSV y simular su descarga', () => {
      const TABLE_FIELDS: IFields = {
        header: [{ value: 'Nombre' }, { value: 'Edad' }],
        body: [{ name: 'name' }, { name: 'age' }],
      };
      const data: IGenericRecord[] = [{ name: 'Ana', age: 30 }, { name: 'Luis', age: 25 }];

      handleExportCSV(TABLE_FIELDS, data);

      expect(document.createElement).toHaveBeenCalledWith('a');
      
      expect(mockLink.href).toBe('mock-url');
      expect(mockLink.download).toBe('documento.csv');
      
      expect(document.body.appendChild).toHaveBeenCalledWith(mockLink);
      expect(mockLink.click).toHaveBeenCalledTimes(1);
      expect(document.body.removeChild).toHaveBeenCalledWith(mockLink);
    });
  });

  describe('handleExportPDF', () => {
    
    const mockNewWindow = {
      document: {
        write: jest.fn(),
        close: jest.fn(),
      },
      focus: jest.fn(),
      print: jest.fn(),
    };

    beforeEach(() => {
        jest.clearAllMocks();
        window.open = jest.fn(() => mockNewWindow as any);
    });

    it('debería abrir una nueva ventana e invocar la impresión si encuentra una tabla', () => {
      document.body.innerHTML = '<table><thead><tr><th>Header</th></tr></thead><tbody><tr><td>Data</td></tr></tbody></table>';
      
      handleExportPDF();

      expect(window.open).toHaveBeenCalledWith('', '', 'width=800,height=600');
      
      expect(mockNewWindow.document.write).toHaveBeenCalledTimes(1);
      expect(mockNewWindow.document.write).toHaveBeenCalledWith(expect.stringContaining('<table>'));
      
      expect(mockNewWindow.document.close).toHaveBeenCalledTimes(1);
      expect(mockNewWindow.focus).toHaveBeenCalledTimes(1);
      expect(mockNewWindow.print).toHaveBeenCalledTimes(1);

      document.body.innerHTML = '';
    });

    it('no debería hacer nada si no encuentra una tabla en el documento', () => {
      document.body.innerHTML = '';
      handleExportPDF();
      expect(window.open).not.toHaveBeenCalled();
    });
  });
});