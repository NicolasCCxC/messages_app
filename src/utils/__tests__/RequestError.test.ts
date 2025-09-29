import { extractErrorMessage } from '@utils/RequestError';

describe('extractErrorMessage', () => {
  it('extrae el primer mensaje del array', () => {
    const err = { message: JSON.stringify({ message: ['Boom', 'Otra'] }) };
    expect(extractErrorMessage(err)).toBe('Boom');
  });

  it('retorna "" cuando el array viene vacío', () => {
    const err = { message: JSON.stringify({ message: [] }) };
    expect(extractErrorMessage(err)).toBe('');
  });

  it('retorna "" cuando no existe "message" en el objeto', () => {
    const err = { message: JSON.stringify({ foo: 'bar' }) };
    expect(extractErrorMessage(err)).toBe('');
  });
});
