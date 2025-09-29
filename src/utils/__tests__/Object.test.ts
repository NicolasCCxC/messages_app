import {
    hasEmptyFields,
    removeEmptyStrings,
    isEmptyObject,
    removeProperties,
  } from '@utils/Object';
  
  describe('object utils', () => {
    describe('hasEmptyFields', () => {
      it('true si alguna key está vacía (string vacío, 0/false no se consideran vacíos aquí a menos que así lo definas)', () => {
        expect(hasEmptyFields({ a: 'x', b: '' })).toBe(true);
      });
  
      it('false si todas las keys tienen valor (no vacío)', () => {
        expect(hasEmptyFields({ a: 'x', b: 'y' })).toBe(false);
      });
  
      it('usa la lista de fields si se envía', () => {
        const src = { a: '', b: 'lleno', c: '' };
        expect(hasEmptyFields(src, ['b'])).toBe(false);
        expect(hasEmptyFields(src, ['a', 'b'])).toBe(true);
      });
    });
  
    describe('removeEmptyStrings', () => {
      it('elimina solo propiedades con string vacío', () => {
        const src = { a: '', b: 'ok', c: null, d: undefined, e: 0, f: false } as any;
        const res = removeEmptyStrings(src);
        expect(res).toEqual({ b: 'ok', c: null, d: undefined, e: 0, f: false });
      });
    });
  
    describe('isEmptyObject', () => {
      it('true si no hay keys', () => {
        expect(isEmptyObject({})).toBe(true);
      });
      it('false si hay al menos una key', () => {
        expect(isEmptyObject({ a: 1 })).toBe(false);
      });
    });
  
    describe('removeProperties', () => {
      it('retorna un clon sin las keys indicadas y no muta el source', () => {
        const src = { a: 1, b: 2, c: 3 };
        const res = removeProperties(src, ['b', 'c']);
        expect(res).toEqual({ a: 1 });
        expect(src).toEqual({ a: 1, b: 2, c: 3 });
      });
    });
  });
  