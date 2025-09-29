import { getRequiredFields } from '@utils/GetRequiredFields';

describe('getRequiredFields', () => {
  it('arma spans con color según isFixed y omite los vacíos', () => {
    const campos = [
      { isFixed: true, content: 'Fijo 1' },
      { isFixed: false, inputStructureProduct: { fieldName: 'Var 1' } },
      { isFixed: true, content: '' }, 
      { isFixed: false, inputStructureProduct: { fieldName: '' } },
    ] as any[];

    const html = getRequiredFields(campos);

    expect(html).toBe(
      '<span style="color: #A9A9AC">Fijo 1</span> ; <span style="color: #4B4B4B">Var 1</span>'
    );
  });

  it('retorna string vacío si no hay valores', () => {
    const html = getRequiredFields([{ isFixed: true, content: '' }] as any);
    expect(html).toBe('');
  });
});
