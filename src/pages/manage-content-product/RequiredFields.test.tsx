/** @jest-environment jsdom */
import { render, screen, fireEvent, within } from '@testing-library/react';
import { RequiredFields } from './RequiredFields';

jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ label, value, onChange, placeholder, error }: any) => (
    <div>
      {label && <label htmlFor={`ti-${label}`}>{label}</label>}
      <input
        id={`ti-${label}`}
        aria-label={label || placeholder}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
      />
      {error ? <span data-testid={`err-ti-${label || 'control'}`}>error</span> : null}
    </div>
  ),
}));

jest.mock('@components/select-search', () => ({
  __esModule: true,
  SelectSearch: ({ label, value, options = [], onChangeOption, placeholder, error }: any) => (
    <div>
      {label && <label htmlFor={`sel-${label}`}>{label}</label>}
      <select
        id={`sel-${label}`}
        aria-label={label || placeholder || 'select'}
        value={value || ''}
        onChange={(e) => {
          const opt =
            options.find((o: any) => String(o.value) === e.target.value || o.label === e.target.value) || options[0];
          onChangeOption?.(opt);
        }}
      >
        <option value="">{placeholder || '--'}</option>
        {options.map((o: any) => (
          <option key={String(o.value)} value={String(o.value)}>
            {o.label}
          </option>
        ))}
      </select>
      {error ? <span data-testid={`err-sel-${label || 'control'}`}>error</span> : null}
    </div>
  ),
}));

jest.mock('@constants/DefaultPlaceholder', () => ({
  __esModule: true,
  DEFAULT_PLACEHOLDER: 'ingrese',
}));

jest.mock('@components/form', () => ({
  __esModule: true,
  ENTER: 'Enter',
}));

jest.mock('.', () => ({
  __esModule: true,
  MAX_LENGHT_FIELD: { fixedRequiredfile: 120 },
}));


const baseOptions = [
  { value: 'id1', label: 'Opt 1' },
  { value: 'id2', label: 'Opt 2' },
  { value: 'id3', label: 'Opt 3' },
];

const makeFields = () => ([

  { id: 'f1', isFixed: true, content: 'A', inputProductStructureId: null },

  { id: 'f2', isFixed: false, content: null, inputProductStructureId: 'id2' },

  { id: 'f3', isFixed: false, content: null, inputProductStructureId: null },
]);

describe('RequiredFields', () => {
  it('edita campo fijo (TextInput) y actualiza con updateField', () => {
    const updateField = jest.fn();
    const onAddField = jest.fn();

    render(
      <RequiredFields
        sendModal={false}
        fields={makeFields()}
        updateField={updateField}
        onAddField={onAddField}
        options={baseOptions}
      />
    );


    const controls = screen.getAllByLabelText('Campos requeridos');
    const inputFixed = controls.find(el => el.tagName === 'INPUT') as HTMLInputElement;
    expect(inputFixed).toBeInTheDocument();

    fireEvent.change(inputFixed, { target: { value: 'XYZ' } });

    expect(updateField).toHaveBeenCalledWith(0, { content: 'XYZ' });
  });

  it('selecciona opción en un campo no fijo (SelectSearch) y actualiza inputProductStructureId', () => {
    const updateField = jest.fn();
    const onAddField = jest.fn();
    const fields = makeFields();

    render(
      <RequiredFields
        sendModal={false}
        fields={fields}
        updateField={updateField}
        onAddField={onAddField}
        options={baseOptions}
      />
    );


    const selects = screen.getAllByRole('combobox');

    const lastSelect = selects[1];

    const lastSelectScope = within(lastSelect);
    expect(lastSelectScope.queryByText('Opt 2')).toBeNull();


    fireEvent.change(lastSelect, { target: { value: 'id3' } });
    expect(updateField).toHaveBeenCalledWith(2, { inputProductStructureId: 'id3', content: null });
  });

  it('toggle del checkbox: no fijo -> fijo (limpia select) y fijo -> no fijo (limpia content)', () => {
    const updateField = jest.fn();
    const fields = makeFields();

    render(
      <RequiredFields
        sendModal={false}
        fields={fields}
        updateField={updateField}
        onAddField={jest.fn()}
        options={baseOptions}
      />
    );

    const checks = screen.getAllByRole('checkbox');

    fireEvent.click(checks[0]);
    expect(updateField).toHaveBeenCalledWith(0, { isFixed: false, content: null });


    fireEvent.click(checks[1]);
    expect(updateField).toHaveBeenCalledWith(1, { isFixed: true, inputProductStructureId: null });
  });

  it('muestra errores cuando sendModal es true y faltan valores en campos', () => {
    const updateField = jest.fn();


    const fields = [
      { id: 'f1', isFixed: true, content: '', inputProductStructureId: null },
      { id: 'f2', isFixed: false, content: null, inputProductStructureId: null },
    ];

    render(
      <RequiredFields
        sendModal
        fields={fields}
        updateField={updateField}
        onAddField={jest.fn()}
        options={baseOptions}
      />
    );


    expect(screen.getByTestId('err-ti-Campos requeridos')).toBeInTheDocument();

    expect(screen.getByTestId('err-sel-Campos requeridos')).toBeInTheDocument();
  });

  it('dispara onAddField por click y por tecla ENTER', () => {
    const onAddField = jest.fn();

    render(
      <RequiredFields
        sendModal={false}
        fields={makeFields()}
        updateField={jest.fn()}
        onAddField={onAddField}
        options={baseOptions}
      />
    );

    const add = screen.getByText('+Agregar campo');

    fireEvent.click(add);
    expect(onAddField).toHaveBeenCalledTimes(1);

    fireEvent.keyDown(add, { key: 'Enter' });
    expect(onAddField).toHaveBeenCalledTimes(2);
  });

  it('muestra error cuando sendModal es true y getSelectedOption no encuentra un valor válido', () => {
    const updateField = jest.fn();

    // Crear un campo con un inputProductStructureId que no existe en las opciones
    const fields = [
      { id: 'f1', isFixed: false, content: null, inputProductStructureId: 'id_inexistente' },
    ];

    render(
      <RequiredFields
        sendModal={true}
        fields={fields}
        updateField={updateField}
        onAddField={jest.fn()}
        options={baseOptions}
      />
    );

    // Verificar que se muestra el error para el SelectSearch
    expect(screen.getByTestId('err-sel-Campos requeridos')).toBeInTheDocument();
  });
});
