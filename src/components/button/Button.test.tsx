import { render, screen, fireEvent } from '@testing-library/react';
import { Button } from './Button';

jest.mock('@components/icon', () => ({
  __esModule: true,
  Icon: ({ name, className }: any) => <span data-testid={`icon-${name}`} className={className} />,
}));

jest.mock('@constants/User', () => ({
  __esModule: true,
  DefaultUserRoles: { Reading: 'Reading' },
}));

const mockUseRole = jest.fn();
jest.mock('@hooks/useRole', () => ({
  __esModule: true,
  useRole: () => mockUseRole(),
}));

describe('Button', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('renderiza el texto y dispara onClick para rol NO Reading', () => {
    mockUseRole.mockReturnValue('Admin');
    const onClick = jest.fn();
    render(<Button text="Enviar" onClick={onClick} />);

    const btn = screen.getByRole('button', { name: 'Enviar' });
    fireEvent.click(btn);
    expect(onClick).toHaveBeenCalledTimes(1);
  });

  it('si isIcon=true renderiza el ícono plusWhite y ajusta el width del texto', () => {
    mockUseRole.mockReturnValue('Admin');
    render(<Button text="Agregar" isIcon onClick={() => {}} />);

    expect(screen.getByTestId('icon-plusWhite')).toBeInTheDocument();
    expect(screen.getByText('Agregar').className).toMatch(/w-\[6\.125rem\]/);
  });

  it('rol Reading + isIcon=true → NO ejecuta onClick', () => {
    mockUseRole.mockReturnValue('Reading');
    const onClick = jest.fn();
    render(<Button text="Agregar" isIcon onClick={onClick} />);

    fireEvent.click(screen.getByRole('button', { name: 'Agregar' }));
    expect(onClick).not.toHaveBeenCalled();
  });

  it('cuando disabled agrega clase de pointer-events-none (estilo bloqueado)', () => {
    mockUseRole.mockReturnValue('Admin');
    render(<Button text="Bloqueado" disabled onClick={() => {}} />);

    const btn = screen.getByRole('button', { name: 'Bloqueado' });
    expect(btn.className).toMatch(/pointer-events-none/);
  });

  it('color=primary aplica clase de fondo azul claro', () => {
    mockUseRole.mockReturnValue('Admin');
    render(<Button text="Primario" color="primary" onClick={() => {}} />);
    const btn = screen.getByRole('button', { name: 'Primario' });
    expect(btn.className).toMatch(/bg-blue-light/);
  });
});
