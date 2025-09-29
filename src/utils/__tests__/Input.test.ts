import { validatePattern } from '../Input';

describe('validatePattern', () => {
  const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  it('true cuando coincide', () => {
    expect(validatePattern('a@b.co', EMAIL)).toBe(true);
  });

  it('false cuando NO coincide', () => {
    expect(validatePattern('invalido', EMAIL)).toBe(false);
  });
});
