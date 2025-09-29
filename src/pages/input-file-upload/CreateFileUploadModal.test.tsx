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
    SelectSearch: ({ value, options, onChangeOption, label, error }: any) => {
      const id = 'select-product';
      return (
        <div>
          <label htmlFor={id}>{label}</label>
          <select
            id={id}
            value={value || ''}
            onChange={(e) => onChangeOption({ value: e.target.value, label: e.target.value })}
          >
            <option value="">--</option>
            {options?.map((o: any) => (
              <option key={o.value} value={o.value}>
                {o.label}
              </option>
            ))}
          </select>
          {error && <span data-testid="err-prod">err</span>}
        </div>
      );
    },
  }));

  jest.mock('@components/text-input', () => ({
    __esModule: true,
    TextInput: ({ label, name, value, onChange, placeholder }: any) => {
      const id = name || String(label || 'input').toLowerCase().replace(/\s+/g, '-');
      return (
        <div>
          <label htmlFor={id}>{label}</label>
          <input id={id} name={name} value={value} onChange={(e) => onChange(e)} placeholder={placeholder} />
        </div>
      );
    },
  }));  

jest.mock('@constants/DefaultPlaceholder', () => ({ __esModule: true, DEFAULT_PLACEHOLDER: 'ingrese' }));
jest.mock('@constants/Validation', () => ({ __esModule: true, REQUIRED_FIELDS: 'Campos obligatorios' }));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) => sel({ productManagement: { allProducts: [{ value: 'P1', label: 'Prod1' }] } }),
}));

jest.mock('@redux/input-file-upload/actions', () => ({
  __esModule: true,
  createFile: (args: any) => ({ type: 'createFile', meta: args }),
}));

import { CreateFileUploadModal } from './CreateFileUploadModal';

describe('CreateFileUploadModal', () => {
  beforeEach(() => jest.clearAllMocks());

  it('muestra mensaje de requeridos si faltan datos', async () => {
    render(
      <CreateFileUploadModal
        toggleModal={jest.fn()}
        toggleToast={jest.fn()}
        handleMessageToast={jest.fn()}
      />
    );

    fireEvent.click(screen.getByText('Guardar'));
    expect(screen.getByText('*Campos obligatorios')).toBeInTheDocument();
  });

  it('envía createFile y cierra con éxito', async () => {
    dispatchMock.mockResolvedValueOnce({ payload: { message: 'OK SUBIDO' } });

    const toggleModal = jest.fn();
    const toggleToast = jest.fn();
    const handleMessageToast = jest.fn();

    render(
      <CreateFileUploadModal
        toggleModal={toggleModal}
        toggleToast={toggleToast}
        handleMessageToast={handleMessageToast}
      />
    );

    fireEvent.change(screen.getByLabelText('Producto'), { target: { value: 'P1' } });
    fireEvent.change(screen.getByLabelText('Periodo'), { target: { value: '202501' } });

    fireEvent.click(screen.getByText('Guardar'));

    await waitFor(() => expect(handleMessageToast).toHaveBeenCalledWith('OK SUBIDO'));
    expect(toggleToast).toHaveBeenCalled();
    expect(toggleModal).toHaveBeenCalled();
  });

  it('muestra error si backend falla', async () => {
    dispatchMock.mockResolvedValueOnce({ error: true, payload: 'Error: {"message":"Falló"}' });

    render(
      <CreateFileUploadModal
        toggleModal={jest.fn()}
        toggleToast={jest.fn()}
        handleMessageToast={jest.fn()}
      />
    );

    fireEvent.change(screen.getByLabelText('Producto'), { target: { value: 'P1' } });
    fireEvent.change(screen.getByLabelText('Periodo'), { target: { value: '202501' } });

    fireEvent.click(screen.getByText('Guardar'));
    await waitFor(() => expect(screen.getByText('*Falló')).toBeInTheDocument());
  });
});
