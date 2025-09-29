/* eslint-disable @typescript-eslint/no-explicit-any */
import { render, screen, fireEvent, act } from '@testing-library/react';
import { CreateRecordModal } from './CreateRecordModal';


jest.mock('@components/modal', () => ({
  Modal: ({ children, onSave }: any) => (
    <div>
      <button onClick={onSave}>SAVE</button>
      {children}
    </div>
  ),
}));


jest.mock('@components/select-search', () => ({
  __esModule: true,
  SelectSearch: ({ label, onChangeOption, value, error }: any) => (
    <div>
      <span>{label}</span>
      <button data-testid={`sel-${label}`} onClick={() => onChangeOption({ label: 'L', value: 'V' })}>
        SEL-{label}
      </button>
      <span data-testid={`val-${label}`}>{value}</span>
      {error && <i>err</i>}
    </div>
  ),
}));


jest.mock('@components/text-input', () => ({
  TextInput: ({ label, name, value, onChange }: any) => (
    <label>
      {label}
      <input aria-label={label} name={name} value={value || ''} onChange={(e) => onChange(e as any)} />
    </label>
  ),
}));


jest.mock('@constants/Validation', () => ({
  REQUIRED_FIELDS: 'Campos requeridos',
  REQUIRED_FIELDS_MESSAGE: 'Debe diligenciar',
}));
jest.mock('@constants/DefaultPlaceholder', () => ({ DEFAULT_PLACEHOLDER: 'PH' }));


jest.mock('.', () => ({
  __esModule: true,
  INPUT_PROPS: {
    productId: { label: 'Producto' },
    fieldName: { label: 'Nombre de campo', name: 'fieldName' },
    registrationIdentifier: { label: 'Identificador', name: 'registrationIdentifier' },
    registrationName: { label: 'Nombre de registro', name: 'registrationName' },
    initialPosition: { label: 'Posición inicial', name: 'initialPosition' },
    endPosition: { label: 'Posición final', name: 'endPosition' },
    indexFileIdentifier: { label: 'Identificador de índice', name: 'indexFileIdentifier' },
  },
  DEFAULT_INPUT: {
    productId: '',
    type: '',
    option: '',
    fieldName: '',
    registrationIdentifier: '',
    registrationName: '',
    initialPosition: '',
    endPosition: '',
    indexFileIdentifier: '',
  },
  REQUIRED_FIELDS: [
    'productId',
    'fieldName',
    'registrationIdentifier',
    'registrationName',
    'initialPosition',
    'endPosition',
  ],
  FieldName: {
    Product: 'productId',
    FieldName: 'fieldName',
    RegistrationIdentifier: 'registrationIdentifier',
    RegistrationName: 'registrationName',
    InitialPosition: 'initialPosition',
    EndPosition: 'endPosition',
    IndexFileIdentifier: 'indexFileIdentifier',
  },
  defaultSelectTypeOptions: [{ label: 'Texto', value: 'text' }],
}));


const hasEmptyFields = jest.fn();
jest.mock('@utils/Object', () => ({
  hasEmptyFields: (...a: any[]) => (hasEmptyFields as any)(...a),
}));


const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  useAppDispatch: () => dispatchMock,
}));

const createInputMock = jest.fn();
jest.mock('@redux/product-input/actions', () => ({
  createInput: (...a: any[]) => createInputMock(...a),
}));

describe('CreateRecordModal', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    // dispatch simula retorno directo (sincrónico)
    dispatchMock.mockImplementation((a: any) => a);
  });

  const baseProps = () => ({
    products: [{ label: 'P1', value: 'p1' }],
    toggleModal: jest.fn(),
    updateNotification: jest.fn(),
  });

  it('muestra validación cuando faltan campos', async () => {
    hasEmptyFields.mockReturnValue(true);
    render(<CreateRecordModal {...baseProps()} />);
    await act(async () => {
      fireEvent.click(screen.getByText('SAVE'));
    });
    expect(screen.getByText('*Campos requeridos')).toBeInTheDocument();
    expect(dispatchMock).not.toHaveBeenCalled();
  });

  it('envía y cierra cuando createInput retorna data', async () => {
    hasEmptyFields.mockReturnValue(false);
    createInputMock.mockReturnValue({
      payload: { data: ['ok'], message: 'created!' },
    });

    const props = baseProps();
    render(<CreateRecordModal {...props} />);

    fireEvent.click(screen.getByTestId('sel-Producto'));
    fireEvent.change(screen.getByLabelText('Nombre de campo'), { target: { value: 'n' } });
    fireEvent.change(screen.getByLabelText('Identificador'), { target: { value: 'id' } });
    fireEvent.change(screen.getByLabelText('Nombre de registro'), { target: { value: 'reg' } });
    fireEvent.change(screen.getByLabelText('Posición inicial'), { target: { value: '1' } });
    fireEvent.change(screen.getByLabelText('Posición final'), { target: { value: '9' } });
    fireEvent.click(screen.getByTestId('sel-Tipo'));

    await act(async () => {
      fireEvent.click(screen.getByText('SAVE'));
    });

    expect(createInputMock).toHaveBeenCalled();
    expect(props.updateNotification).toHaveBeenCalledWith('created!');
    expect(props.toggleModal).toHaveBeenCalled();
  });

  it('muestra mensaje de error de backend si payload.data es null', async () => {
    hasEmptyFields.mockReturnValue(false);
    createInputMock.mockReturnValue({
      payload: { data: null, message: 'backend-error' },
    });

    const props = baseProps();
    render(<CreateRecordModal {...props} />);

    await act(async () => {
      fireEvent.click(screen.getByText('SAVE'));
    });

    expect(screen.getByText('*backend-error')).toBeInTheDocument();
    expect(props.toggleModal).not.toHaveBeenCalled();
  });
});
