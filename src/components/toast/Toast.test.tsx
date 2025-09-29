import { render, screen, act } from '@testing-library/react';
import { Toast } from './Toast';
import { NotificationType } from '.';

jest.mock('@components/icon', () => ({
  __esModule: true,
  Icon: ({ name }: any) => <span data-testid={`icon-${name}`} />,
}));

describe('Toast', () => {
  beforeEach(() => {
    jest.useFakeTimers();
  });
  afterEach(() => {
    jest.runOnlyPendingTimers();
    jest.useRealTimers();
  });

  it('muestra el mensaje y la clase según type=Error', () => {
    render(
      <Toast
        open
        type={NotificationType.Error}
        message="Ocurrió un error"
        autoHideDuration={3000}
        onClose={() => {}}
      />
    );

    // Mensaje visible
    expect(screen.getByText('Ocurrió un error')).toBeInTheDocument();

    // Clase aplicada
    const container = screen.getByText('Ocurrió un error').closest('.toast');
    expect(container?.className).toMatch(/toast--error/);

    // Icono de error
    expect(screen.getByTestId('icon-exclamationRed')).toBeInTheDocument();
  });

  it('con type distinto a Error usa el ícono "checkCircle" y hace auto-hide', () => {
    const onClose = jest.fn();
    render(
      <Toast
        open
        message="Hecho"
        autoHideDuration={100}
        onClose={onClose}
      />
    );

    expect(screen.getByText('Hecho')).toBeInTheDocument();
    expect(screen.getByTestId('icon-checkCircle')).toBeInTheDocument();

    // Avanzar timers para que dispare autoHide y llame onClose
    act(() => {
      jest.advanceTimersByTime(120);
    });
    expect(onClose).toHaveBeenCalled();
  });
});
