import { render, screen, fireEvent } from '@testing-library/react';
import { SelectSearch } from './SelectSearch';

jest.mock('@components/icon', () => ({
  Icon: (props: any) => <span data-testid="icon" {...props} />,
}));

jest.mock('@components/form', () => ({ ENTER: 'Enter' }));

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
});
