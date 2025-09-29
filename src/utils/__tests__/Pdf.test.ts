jest.mock('@constants/Pdf', () => ({
    __esModule: true,
    CM_PER_INCH: 2.54,
    DPI: 96,
  }));
  
  import { cmToPx, getCssMarginVars, convertMarginsToPx } from '@utils/Pdf';
  
  describe('pdf margins utils', () => {
    it('cmToPx convierte correctamente', () => {
      expect(cmToPx(2.54)).toBeCloseTo(96, 5);
    });
  
    it('getCssMarginVars devuelve variables CSS con px', () => {
      const vars = getCssMarginVars({ top: 10, right: 5, bottom: 0, left: 7 } as any);
      expect(vars).toEqual({
        '--mt': '10px',
        '--mr': '5px',
        '--mb': '0px',
        '--ml': '7px',
      });
    });
  
    it('convertMarginsToPx convierte cada lado', () => {
      const res = convertMarginsToPx({ top: 2.54, right: 1, bottom: 0, left: 0.5 } as any);
      expect(res.top).toBeCloseTo(96, 5);
      expect(res.right).toBeCloseTo((1 / 2.54) * 96, 5);
      expect(res.bottom).toBeCloseTo(0, 5);
      expect(res.left).toBeCloseTo((0.5 / 2.54) * 96, 5);
    });
  });
  