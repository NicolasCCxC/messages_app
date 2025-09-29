import {
    replaceItem,
    addItem,
    deleteItem,
    isEmptyValue,
    hasEmptyFields,
    filterData,
  } from '@utils/Array';
  
  type Rec = { id?: string; [k: string]: any };
  
  describe('utils: collections', () => {
    it('replaceItem reemplaza por id', () => {
      const current: Rec[] = [{ id: '1', a: 1 }, { id: '2', a: 2 }];
      const next = replaceItem(current, { id: '2', a: 99 });
      expect(next).toEqual([{ id: '1', a: 1 }, { id: '2', a: 99 }]);
    });
  
    it('addItem agrega si trae id', () => {
      const current: Rec[] = [{ id: '1' }];
      expect(addItem(current, { id: '2' })).toEqual([{ id: '1' }, { id: '2' }]);
      expect(addItem(current, { id: '' })).toEqual([{ id: '1' }]);
    });
  
    it('deleteItem elimina por id', () => {
      const current: Rec[] = [{ id: '1' }, { id: '2' }];
      expect(deleteItem(current, '1')).toEqual([{ id: '2' }]);
    });
  
    it('isEmptyValue cubre strings/obj/número/null', () => {
      expect(isEmptyValue('')).toBe(true);
      expect(isEmptyValue('  ')).toBe(true);
      expect(isEmptyValue({})).toBe(true);
      expect(isEmptyValue({ a: 1 })).toBe(false);
      expect(isEmptyValue(0 as unknown as number)).toBe(false);
      expect(isEmptyValue(null as unknown as number)).toBe(true);
      expect(isEmptyValue(undefined as unknown as number)).toBe(true);
    });
  
    it('hasEmptyFields detecta vacíos en objeto', () => {
      expect(hasEmptyFields({ a: '', b: 1 } as Rec)).toBe(true);
      expect(hasEmptyFields({ a: 'ok', b: 1 } as Rec)).toBe(false);
    });
  
    it('filterData filtra case-insensitive por key + value', () => {
      const data: Rec[] = [{ id: '1', name: 'Juan' }, { id: '2', name: 'Ana' }];
      const res = filterData(data, { key: 'name', value: 'ju' });
      expect(res).toEqual([{ id: '1', name: 'Juan' }]);
    });
  });
  