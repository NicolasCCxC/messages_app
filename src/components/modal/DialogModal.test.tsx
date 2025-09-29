import { render, screen, fireEvent } from '@testing-library/react';
import { DialogModal } from './DialogModal';

jest.mock('@components/button', () => ({
  Button: ({ text, onClick }: any) => (
    <button data-testid={`btn-${text}`} onClick={onClick}>
      {text}
    </button>
  ),
}));

describe('DialogModal', () => {
  it('renderiza título/descripcion y dispara onClose/onConfirm', () => {
    const onClose = jest.fn();
    const onConfirm = jest.fn();

    const data = {
      title: 'Eliminar',
      description: '¿Seguro?',
      rightButtonText: 'Confirmar',
    };

    render(<DialogModal data={data as any} onClose={onClose} onConfirm={onConfirm} />);

    expect(screen.getByText('Eliminar')).toBeInTheDocument();
    expect(screen.getByText('¿Seguro?')).toBeInTheDocument();

    fireEvent.click(screen.getByTestId('btn-Cerrar'));
    expect(onClose).toHaveBeenCalled();

    fireEvent.click(screen.getByTestId('btn-Confirmar'));
    expect(onConfirm).toHaveBeenCalled();
  });
});
