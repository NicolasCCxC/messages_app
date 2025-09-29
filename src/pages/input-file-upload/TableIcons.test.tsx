/** @jest-environment jsdom */
import { render, screen, fireEvent, waitFor } from '@testing-library/react';

jest.mock('@components/icon', () => ({
  __esModule: true,
  Icon: ({ onClick }: any) => <button onClick={onClick}>cancel</button>,
}));
jest.mock('@components/modal', () => ({
  __esModule: true,
  DialogModalType: { CancelProcess: 'CancelProcess' },
  DialogModal: ({ onClose, onConfirm }: any) => (
    <div data-testid="dialog">
      <button onClick={onConfirm}>confirm</button>
      <button onClick={onClose}>close</button>
    </div>
  ),
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
}));

jest.mock('@redux/input-file-upload/actions', () => ({
  __esModule: true,
  cancelFile: (id: string) => ({ type: 'cancelFile', meta: id }),
}));

jest.mock('.', () => ({ __esModule: true, statusActive: 'ACTIVE' }));

import { TableIcons } from './TableIcons';

describe('TableIcons', () => {
  beforeEach(() => jest.clearAllMocks());

  it('muestra icono si status es ACTIVE y confirma cancelación', async () => {
    dispatchMock.mockResolvedValueOnce({ payload: { message: 'Cancelado OK' } });

    const handleMessageToast = jest.fn();
    const toggleToast = jest.fn();

    render(
      <TableIcons
        item={{ id: '123', status: 'ACTIVE' } as any}
        handleMessageToast={handleMessageToast}
        toggleToast={toggleToast}
      />
    );

    fireEvent.click(screen.getByText('cancel')); 

    expect(screen.getByTestId('dialog')).toBeInTheDocument();

    fireEvent.click(screen.getByText('confirm'));

    await waitFor(() => expect(handleMessageToast).toHaveBeenCalledWith('Cancelado OK'));
    expect(toggleToast).toHaveBeenCalled();
  });

  it('no muestra icono si status no es ACTIVE', () => {
    render(
      <TableIcons
        item={{ id: '1', status: 'FINISHED' } as any}
        handleMessageToast={jest.fn()}
        toggleToast={jest.fn()}
      />
    );

    expect(screen.queryByText('cancel')).not.toBeInTheDocument();
  });
});
