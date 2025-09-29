import { renderHook } from '@testing-library/react';
import { useTableSelection } from '../useTableSelection';
import { IElement } from '@pages/object-manage-format/context';
import { ObjectType } from '@constants/ObjectsEditor';

const mockElement: IElement = {
  id: '1',
  productId: 'prod-1',
  name: 'Mock Table',
  identifier: 'table-01',
  objectType: ObjectType.Table,
  type: 'table',
  header: {
    columns: [
      { id: 'h1', value: 'Header 1', style: {}, colSpan: 1 },
      { id: 'h2', value: 'Header 2', style: {}, colSpan: 1 },
      { id: 'h3', value: 'Header 3', style: {}, colSpan: 1 },
    ],
  },
  body: {
    cells: [
      { id: 'c1', rowIndex: 0, columnIndex: 0, value: 'R0C0', style: {}, colSpan: 1 },
      { id: 'c2', rowIndex: 0, columnIndex: 1, value: 'R0C1', style: {}, colSpan: 1 },
      { id: 'c3', rowIndex: 0, columnIndex: 2, value: 'R0C2', style: {}, colSpan: 1 },
      { id: 'c4', rowIndex: 1, columnIndex: 0, value: 'R1C0', style: {}, colSpan: 1 },
    ],
  },
};

describe('useTableSelection hook', () => {

  it('debería retornar un array vacío si la fila de inicio y fin son diferentes', () => {
    const { result } = renderHook(() => useTableSelection({ element: mockElement }));
    const selection = result.current.getHorizontalSelection(
      { row: 0, column: 0 },
      { row: 1, column: 1 }
    );
    expect(selection).toEqual([]);
  });

  it('debería seleccionar correctamente las columnas del header cuando row es -1', () => {
    const { result } = renderHook(() => useTableSelection({ element: mockElement }));
    const selection = result.current.getHorizontalSelection(
      { row: -1, column: 0 },
      { row: -1, column: 1 }
    );
    
    expect(selection).toHaveLength(2);
    expect(selection[0].column).toBe(0);
    expect(selection[1].column).toBe(1);
    expect(selection[0].row).toBe(-1);
  });

  it('debería seleccionar correctamente las celdas del body', () => {
    const { result } = renderHook(() => useTableSelection({ element: mockElement }));
    const selection = result.current.getHorizontalSelection(
      { row: 0, column: 1 },
      { row: 0, column: 2 }
    );
    
    expect(selection).toHaveLength(2);
    expect(selection[0].column).toBe(1);
    expect(selection[1].column).toBe(2);
    expect(selection[0].row).toBe(0);
  });

  it('debería funcionar correctamente si el inicio de la selección es mayor que el fin', () => {
    const { result } = renderHook(() => useTableSelection({ element: mockElement }));
    const selection = result.current.getHorizontalSelection(
      { row: 0, column: 2 },
      { row: 0, column: 1 }
    );

    expect(selection).toHaveLength(2);
    expect(selection[0].column).toBe(1);
    expect(selection[1].column).toBe(2);
  });

  it('debería manejar el caso de no tener header o body definidos', () => {
    const simpleElement: IElement = { 
        id: '2', 
        type: 'table',
        productId: 'prod-2',
        name: 'Simple Table',
        identifier: 'table-02',
        objectType: ObjectType.Table,
    };
    const { result } = renderHook(() => useTableSelection({ element: simpleElement }));
    
    const headerSelection = result.current.getHorizontalSelection({ row: -1, column: 0 }, { row: -1, column: 1 });
    const bodySelection = result.current.getHorizontalSelection({ row: 0, column: 0 }, { row: 0, column: 1 });

    expect(headerSelection).toBeUndefined();
    expect(bodySelection).toBeUndefined();
    });
});