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
  SelectSearch: ({ value, options, onChangeOption, error }: any) => (
    <div>
      <select
        aria-label="producto"
        value={value || ''}
        onChange={(e) => onChangeOption({ value: e.target.value, label: e.target.value })}
      >
        <option value="">--</option>
        {options?.map((o: any) => (
          <option key={o.value} value={o.value}>{o.label}</option>
        ))}
      </select>
      {error && <span data-testid="error-product">error</span>}
    </div>
  ),
}));
jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ label, name, value, onChange, error, disabled, placeholder }: any) => (
    <div>
      <label>{label}</label>
      <input
        aria-label={name || label}
        name={name}
        placeholder={placeholder}
        value={value}
        disabled={disabled}
        onChange={(e) => onChange(e)}
      />
      {error && <span data-testid={`error-${name || label}`}>error</span>}
    </div>
  ),
}));

jest.mock('@constants/DefaultPlaceholder', () => ({ __esModule: true, DEFAULT_PLACEHOLDER: 'ingrese' }));
jest.mock('@constants/Validation', () => ({ __esModule: true, REQUIRED_FIELDS: 'Campos obligatorios' }));

jest.mock('@redux/executing-index-generation/actions', () => ({
  __esModule: true,
  createIndex: (args: any) => ({ type: 'createIndex', meta: args }),
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) =>
    sel({ productManagement: { allProducts: [{ value: 'X', label: 'Producto X' }] } }),
}));

import { ExecutingIndexGenerationModal } from './ExecutingIndexGenerationModal';

describe('ExecutingIndexGenerationModal', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('valida campos requeridos y muestra mensaje', async () => {
    render(
      <ExecutingIndexGenerationModal
        toggleModal={jest.fn()}
        toggleToast={jest.fn()}
        handleMessageToast={jest.fn()}
      />
    );

    fireEvent.click(screen.getByText('Guardar'));
    expect(screen.getByText('*Campos obligatorios')).toBeInTheDocument();
  });

  it('envía createIndex y cierra con éxito', async () => {
    dispatchMock.mockResolvedValueOnce({
      payload: { message: 'OK X-202501' },
    });

    const toggleModal = jest.fn();
    const toggleToast = jest.fn();
    const handleMessageToast = jest.fn();

    render(
      <ExecutingIndexGenerationModal
        toggleModal={toggleModal}
        toggleToast={toggleToast}
        handleMessageToast={handleMessageToast}
      />
    );

    fireEvent.change(screen.getByLabelText('producto'), { target: { value: 'X' } });
    fireEvent.change(screen.getByLabelText('Periodo'), { target: { value: '202501' } });

    fireEvent.click(screen.getByText('Guardar'));

    await waitFor(() => expect(handleMessageToast).toHaveBeenCalledWith('OK X-202501'));
    expect(toggleToast).toHaveBeenCalled();
    expect(toggleModal).toHaveBeenCalled();
  });

  it('muestra error si la acción retorna error', async () => {
    dispatchMock.mockResolvedValueOnce({
      error: true,
      payload: 'Error: {"message":"Fallo"}',
    });

    render(
      <ExecutingIndexGenerationModal
        toggleModal={jest.fn()}
        toggleToast={jest.fn()}
        handleMessageToast={jest.fn()}
      />
    );

    fireEvent.change(screen.getByLabelText('producto'), { target: { value: 'X' } });
    fireEvent.change(screen.getByLabelText('Periodo'), { target: { value: '202501' } });

    fireEvent.click(screen.getByText('Guardar'));

    await waitFor(() => expect(screen.getByText('*Fallo')).toBeInTheDocument());
  });
});
