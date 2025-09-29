/** @jest-environment jsdom */
import { render, screen, fireEvent, waitFor } from '@testing-library/react';

jest.mock('@components/modal', () => ({
  __esModule: true,
  Modal: ({ title, onSave, onClose, children }: any) => (
    <div>
      <h2>{title}</h2>
      {children}
      <button onClick={onSave}>Guardar</button>
      <button onClick={onClose}>Cerrar</button>
    </div>
  ),
}));
jest.mock('@components/select-search', () => ({
  __esModule: true,
  SelectSearch: ({ value, options, onChangeOption, label }: any) => (
    <div>
      <label>{label}</label>
      <select
        aria-label="producto"
        value={value || ''}
        onChange={(e) => onChangeOption({ value: e.target.value, label: e.target.value })}
      >
        <option value="">--</option>
        {options?.map((o: any) => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
    </div>
  ),
}));
jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ label, name, value, onChange, maxLength }: any) => (
    <div>
      <label>{label}</label>
      <input aria-label={name} name={name} value={value} onChange={(e)=>onChange(e)} maxLength={maxLength}/>
    </div>
  ),
}));

const REQUIRED_FIELDS_TEXT = 'Campos requeridos';
jest.mock('@constants/Validation', () => ({ __esModule: true, REQUIRED_FIELDS: 'Campos requeridos' }));

const hasEmptyFieldsMock = jest.fn();
jest.mock('@utils/Object', () => ({
  __esModule: true,
  hasEmptyFields: (...args: any[]) => (hasEmptyFieldsMock as any)(...args),
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
}));

jest.mock('@redux/paths/actions', () => ({
  __esModule: true,
  createPath: (args: any) => ({ type: 'createPath', meta: args }),
}));

jest.mock('.', () => ({
  __esModule: true,
  DEFAULT_FORM_VALUES: {
    productId: '',
    option: '',
    routeOutputExtract: '',
    routeOutputIndex: '',
  },
  MAX_OUTBOUND_PATH_LENGTH: 255,
}));

import { CreateRecordModal } from './CreateRecordModal';

describe('CreateRecordModal', () => {
  const products = [{ value: 'p1', label: 'Prod 1' }];

  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('muestra error si hay campos vacíos', async () => {
    hasEmptyFieldsMock.mockReturnValue(true);
    const toggleModal = jest.fn();
    const updateNotification = jest.fn();

    render(
      <CreateRecordModal
        products={products as any}
        toggleModal={toggleModal}
        updateNotification={updateNotification}
      />
    );

    fireEvent.click(screen.getByText('Guardar'));
    expect(screen.getByText(`*${REQUIRED_FIELDS_TEXT}`)).toBeInTheDocument();
    expect(dispatchMock).not.toHaveBeenCalled();
  });

  it('envía createPath, notifica y cierra', async () => {
    hasEmptyFieldsMock.mockReturnValue(false);
    dispatchMock.mockResolvedValueOnce({ payload: { message: 'Creado OK' } });

    const toggleModal = jest.fn();
    const updateNotification = jest.fn();

    render(
      <CreateRecordModal
        products={products as any}
        toggleModal={toggleModal}
        updateNotification={updateNotification}
      />
    );

    fireEvent.change(screen.getByLabelText('producto'), { target: { value: 'p1' } });
    fireEvent.change(screen.getByLabelText('routeOutputExtract'), { target: { name: 'routeOutputExtract', value: '/ruta/extracto' } });
    fireEvent.change(screen.getByLabelText('routeOutputIndex'), { target: { name: 'routeOutputIndex', value: '/ruta/indice' } });

    fireEvent.click(screen.getByText('Guardar'));

    await waitFor(() => expect(updateNotification).toHaveBeenCalledWith('Creado OK'));
    expect(toggleModal).toHaveBeenCalled();
  });
});
