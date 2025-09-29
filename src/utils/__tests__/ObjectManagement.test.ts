import { getBorders } from '@utils/ObjectManagement';
import type { CSSProperties } from 'react';

describe('getBorders', () => {
  it('devuelve 0 si no hay style', () => {
    expect(getBorders()).toEqual({
      borderTopLeftRadius: 0,
      borderTopRightRadius: 0,
      borderBottomLeftRadius: 0,
      borderBottomRightRadius: 0,
    });
  });

  it('parsea números o strings con px', () => {
    const style: CSSProperties = {
      borderTopLeftRadius: '10px',
      borderTopRightRadius: 8,
      borderBottomLeftRadius: '0',
      borderBottomRightRadius: undefined,
    };
    expect(getBorders(style)).toEqual({
      borderTopLeftRadius: 10,
      borderTopRightRadius: 8,
      borderBottomLeftRadius: 0,
      borderBottomRightRadius: 0,
    });
  });
});
