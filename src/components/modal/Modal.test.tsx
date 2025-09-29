import { render, screen, fireEvent } from '@testing-library/react';
import { Modal } from './Modal';

jest.mock('@components/button', () => ({
  Button: ({ text, onClick, buttonClassName }: any) => (
    <button data-testid={`btn-${text}`} className={buttonClassName} onClick={onClick}>
      {text}
    </button>
  ),
}));

describe('Modal', () => {
  it('renderiza título y children', () => {
    render(
      <Modal open title="Mi modal" onClose={jest.fn()}>
        <div>contenido</div>
      </Modal>
    );

    expect(screen.getByText('Mi modal')).toBeInTheDocument();
    expect(screen.getByText('contenido')).toBeInTheDocument();
  });

  it('muestra botones por defecto y dispara onClose / onSave', () => {
    const onClose = jest.fn();
    const onSave = jest.fn();

    render(
      <Modal open title="t" onClose={onClose} onSave={onSave}>
        <div />
      </Modal>
    );

    fireEvent.click(screen.getByTestId('btn-Cerrar'));
    expect(onClose).toHaveBeenCalled();

    fireEvent.click(screen.getByTestId('btn-Crear'));
    expect(onSave).toHaveBeenCalled();
  });

  it('respeta saveButtonText', () => {
    const onSave = jest.fn();
    render(
      <Modal open title="t" onClose={jest.fn()} onSave={onSave} saveButtonText="Guardar">
        <div />
      </Modal>
    );

    expect(screen.getByTestId('btn-Guardar')).toBeInTheDocument();
  });

  it('oculta zona de botones si noButtons=true', () => {
    render(
      <Modal open title="t" onClose={jest.fn()} noButtons>
        <div />
      </Modal>
    );

    expect(screen.queryByTestId('btn-Cerrar')).not.toBeInTheDocument();
    expect(screen.queryByTestId('btn-Crear')).not.toBeInTheDocument();
  });
});
