import { generateRandomString } from '@utils/GenerateRandomString';

describe('generateRandomString', () => {
  afterEach(() => {
    jest.restoreAllMocks();
  });

  it('devuelve 16 caracteres por defecto', () => {
    const s = generateRandomString();
    expect(typeof s).toBe('string');
    expect(s).toHaveLength(16);
    expect(/^[0-9A-Za-z]+$/.test(s)).toBe(true);
  });

  it('devuelve la longitud solicitada', () => {
    const s = generateRandomString(8);
    expect(s).toHaveLength(8);
  });

  it('cuando length=0, usa longitud aleatoria; podemos forzarla a 30', () => {
    const seq = [0.5, ...Array(30).fill(0)];
    let i = 0;
    jest.spyOn(Math, 'random').mockImplementation(() => seq[i++] ?? 0);

    const s = generateRandomString(0);
    expect(s).toHaveLength(30);
    expect(/^0+$/.test(s)).toBe(true);
  });
});
