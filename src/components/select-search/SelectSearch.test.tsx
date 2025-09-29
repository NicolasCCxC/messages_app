import { render, screen, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { SelectSearch } from './SelectSearch';

jest.mock('@components/icon', () => ({
  Icon: (props: any) => <span data-testid="icon" {...props} />,
}));

jest.mock('@components/form', () => ({ ENTER: 'Enter' }));

// Mock useOutsideClick hook
// Store the last callback in a variable that can be accessed by tests
let lastOutsideClickCallback: (() => void) | null = null;

jest.mock('@hooks/useOutsideClick', () => ({
  useOutsideClick: (callback: () => void) => {
    // Store the callback in the variable for tests to access
    lastOutsideClickCallback = callback;
    const ref = { current: document.createElement('div') };
    return ref;
  },
  // Expose the callback for tests
  __getLastCallback: () => lastOutsideClickCallback,
}));

describe('SelectSearch', () => {
  const options = [
    { id: '1', value: '1', label: 'Uno' },
    { id: '2', value: '2', label: 'Dos' },
    { id: '3', value: '3', label: 'Tres' },
  ];

  it('muestra label y placeholder; abre/cierra con click y filtra', () => {
    render(
      <SelectSearch
        label="Mi Label"
        value=''
        placeholder="Elige..."
        options={options as any}
        onChangeOption={jest.fn()}
        name="campo"
      />
    );

    expect(screen.getByText('Mi Label')).toBeInTheDocument();
    expect(screen.getByText('Elige...')).toBeInTheDocument();

    // abrir
    fireEvent.click(screen.getByText('Elige...'));
    // debería listar todas
    expect(screen.getByText('Uno')).toBeInTheDocument();
    expect(screen.getByText('Dos')).toBeInTheDocument();
    expect(screen.getByText('Tres')).toBeInTheDocument();

    // filtrar escribiendo "Do"
    const input = screen.getByRole('textbox');
    fireEvent.change(input, { target: { value: 'Do' } });

    expect(screen.queryByText('Uno')).not.toBeInTheDocument();
    expect(screen.getByText('Dos')).toBeInTheDocument();
    expect(screen.queryByText('Tres')).not.toBeInTheDocument();

    fireEvent.click(screen.getByText('Dos'));
  });

  it('selecciona una opción y llama onChangeOption con (option, name)', () => {
    const onChange = jest.fn();
    render(
      <SelectSearch
        label="L"
        value=''
        options={options as any}
        onChangeOption={onChange}
        name="miSelect"
        placeholder="p"
      />
    );

    fireEvent.click(screen.getByText('p'));
    fireEvent.click(screen.getByText('Tres'));

    expect(onChange).toHaveBeenCalledWith(
      { id: '3', value: '3', label: 'Tres' },
      'miSelect'
    );
  });

  it('abre con Enter y permite seleccionar con Enter', () => {
    const onChange = jest.fn();
    render(
      <SelectSearch
        label="L"
        value=''
        options={options as any}
        onChangeOption={onChange}
        name="sel"
        placeholder="elige"
      />
    );

    const opener = screen.getByText('elige').parentElement?.parentElement as HTMLElement;
    fireEvent.keyDown(opener, { key: 'Enter' });

    const li = screen.getByText('Uno');
    fireEvent.keyDown(li, { key: 'Enter' });

    expect(onChange).toHaveBeenCalledWith(
      { id: '1', value: '1', label: 'Uno' },
      'sel'
    );
  });

  it('muestra el valor seleccionado cuando se proporciona un value', () => {
    render(
      <SelectSearch
        label="Selección"
        value='2'
        options={options as any}
        onChangeOption={jest.fn()}
        name="seleccion"
      />
    );

    // Debería mostrar el label de la opción con id='2'
    expect(screen.getByText('Dos')).toBeInTheDocument();
  });

  it('aplica clases de error cuando error=true', () => {
    render(
      <SelectSearch
        label="Con Error"
        value=''
        options={options as any}
        onChangeOption={jest.fn()}
        placeholder="Selecciona"
        error={true}
      />
    );

    // Verificar que la clase de error está aplicada
    const container = screen.getByText('Selecciona').parentElement;
    expect(container).toHaveClass('border-red-error');
  });

  it('cierra el dropdown cuando se hace click fuera', async () => {
    render(
      <SelectSearch
        value=''
        options={options as any}
        onChangeOption={jest.fn()}
        placeholder="Click fuera"
      />
    );

    // Abrir el dropdown
    fireEvent.click(screen.getByText('Click fuera'));

    // Verificar que el dropdown está abierto
    expect(screen.getByText('Uno')).toBeInTheDocument();

    // Simular click fuera llamando al callback del hook mockeado
    const mockHook = jest.requireMock('@hooks/useOutsideClick');
    const callback = mockHook.__getLastCallback();
    if (callback) callback();

    // Verificar que el dropdown está cerrado
    expect(screen.queryByText('Uno')).not.toBeInTheDocument();
  });

  it('muestra "No se encontraron resultados" cuando no hay coincidencias', async () => {
    const user = userEvent.setup();
    render(
      <SelectSearch
        value=''
        options={options as any}
        onChangeOption={jest.fn()}
        placeholder="Buscar"
      />
    );

    // Abrir el dropdown
    await user.click(screen.getByText('Buscar'));

    // Buscar algo que no existe
    const input = screen.getByRole('textbox');
    await user.type(input, 'xyz');

    // Verificar mensaje de no resultados
    expect(screen.getByText('No se encontraron resultados')).toBeInTheDocument();
  });

  it('maneja correctamente un array de opciones vacío', () => {
    render(
      <SelectSearch
        value=''
        options={[]}
        onChangeOption={jest.fn()}
        placeholder="Sin opciones"
      />
    );

    // Abrir el dropdown
    fireEvent.click(screen.getByText('Sin opciones'));

    // Verificar mensaje de no resultados
    expect(screen.getByText('No se encontraron resultados')).toBeInTheDocument();
  });

  it('aplica las clases CSS personalizadas correctamente', () => {
    render(
      <SelectSearch
        value=''
        options={options as any}
        onChangeOption={jest.fn()}
        placeholder="Clases"
        inputClassName="test-input"
        labelClassName="test-label"
        wrapperClassName="test-wrapper"
        containerClassName="test-container"
        iconClassName="test-icon"
        label="Etiqueta"
      />
    );

    // Verificar clases aplicadas
    expect(screen.getByText('Etiqueta')).toHaveClass('test-label');
    expect(screen.getByText('Clases')).toHaveClass('test-input');
    expect(screen.getByText('Clases').parentElement).toHaveClass('test-container');
    expect(screen.getByTestId('icon')).toHaveClass('test-icon');
    expect(screen.getByText('Etiqueta').parentElement?.parentElement).toHaveClass('test-wrapper');
  });
});
