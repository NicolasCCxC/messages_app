import { getBaseUrl } from '@utils/env';

const ORIGINAL_ENV = process.env;

describe('getBaseUrl', () => {
  beforeEach(() => {
    jest.resetModules();
    process.env = { ...ORIGINAL_ENV };
  });

  afterAll(() => {
    process.env = ORIGINAL_ENV;
  });

  it('retorna process.env.VITE_BASE_URL si existe', () => {
    process.env.VITE_BASE_URL = 'http://from-process';
    expect(getBaseUrl()).toBe('http://from-process');
  });

  it('retorna "" si no hay process.env ni Vite import.meta', () => {
    delete (process.env as any).VITE_BASE_URL;
    expect(getBaseUrl()).toBe('');
  });
});
