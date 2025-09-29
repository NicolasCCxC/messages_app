import { getIconName, getIconVariant } from '@utils/Icon';

jest.mock('@constants/Icon', () => ({
  __esModule: true,
  IconColor: {
    Default: 'default',
    Primary: 'primary',
    Danger: 'danger',
  },
}));

describe('icons utils', () => {
  it('getIconName transforma CamelCase a kebab con prefijo en mayúsculas', () => {
    expect(getIconName('ChevronDown')).toBe('-chevron-down');
    expect(getIconName('wifi')).toBe('wifi');
    expect(getIconName('X')).toBe('-x');
    expect(getIconName('XMLHttp')).toBe('-x-m-l-http');
  });

  it('getIconVariant reemplaza el color por el variant', () => {
    expect(getIconVariant('arrow-default-24' as any, 'primary' as any)).toBe('arrow-primary-24');
    expect(getIconVariant('arrow-plain-24' as any, 'primary' as any)).toBe('arrow-plain-24');
  });
});
