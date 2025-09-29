import { renderHook, act } from '@testing-library/react';
import { useTableData } from '../useTableData';
import { IGenericRecord } from '@models/GenericRecord';

const initialData: IGenericRecord[] = [
  { id: 1, name: 'Item 1', value: 'A' },
  { id: 2, name: 'Item 2', value: 'B' },
];

describe('useTableData hook', () => {
  
  it('debería inicializar el estado con los datos proporcionados', () => {
    const { result } = renderHook(() => useTableData(initialData));
    expect(result.current.data).toEqual(initialData);
  });

  it('debería actualizar un campo específico con onFieldChange', () => {
    const { result } = renderHook(() => useTableData(initialData));

    act(() => {
      result.current.onFieldChange('New Value', { 
        row: 0, 
        item: { name: 'name' } 
      });
    });

    expect(result.current.data[0].name).toBe('New Value');
    expect(result.current.data[1].name).toBe('Item 2');
  });

  it('debería reemplazar completamente los datos con updateData', () => {
    const { result } = renderHook(() => useTableData(initialData));
    const newData = [{ id: 3, name: 'New Item' }];

    act(() => {
      result.current.updateData(newData);
    });

    expect(result.current.data).toEqual(newData);
  });

  it('debería fusionar los datos cuando los datos de entrada (allData) cambian, preservando ediciones locales', () => {
    const { result, rerender } = renderHook(({ allData }) => useTableData(allData), {
      initialProps: { allData: initialData },
    });
    
    act(() => {
        result.current.onFieldChange('Edited Name', { row: 0, item: { name: 'name' } });
    });
    
    expect(result.current.data[0].name).toBe('Edited Name');

    const updatedAllData = [
      { id: 1, name: 'Item 1 Updated', value: 'A' },
      { id: 2, name: 'Item 2', value: 'B' },
      { id: 3, name: 'Item 3 New', value: 'C' },
    ];
    
    rerender({ allData: updatedAllData });

    expect(result.current.data).toHaveLength(3);
    expect(result.current.data.find(d => d.id === 1)?.name).toBe('Edited Name');
    expect(result.current.data.find(d => d.id === 3)).toBeDefined();
  });
});