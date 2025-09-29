/* eslint-disable @typescript-eslint/no-explicit-any */
import { render, screen, fireEvent, act } from '@testing-library/react';
import { CreateProductModal } from './CreateProductModal';

jest.mock('@components/modal', () => ({
    Modal: ({ children, onSave }: any) => (
        <div>
            <button onClick={onSave}>Crear</button>
            {children}
        </div>
    ),
}));

jest.mock('@components/select-search', () => ({
    __esModule: true,
    SelectSearch: ({ label, onChangeOption, value, error }: any) => (
        <div>
            <span>{label}</span>
            <button data-testid={`sel-${label}`} onClick={() => onChangeOption({ label: 'Activo', value: 'true' })}>
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
            <input aria-label={label} name={name} value={value || ''} onChange={e => onChange(e as any)} />
        </label>
    ),
}));

jest.mock('@constants/Validation', () => ({
    REQUIRED_FIELDS: 'Campos requeridos',
    REQUIRED_FIELDS_MESSAGE: 'Debe diligenciar',
}));

jest.mock('.', () => ({
    __esModule: true,
    DEFAULT_FORM_VALUES: {
        code: '',
        description: '',
        documentType: '',
    },
    DEFAULT_SELECT_STATE: { label: '', value: '' },
    MaxLengthField: {
        Description: 100,
        Code: 6,
    },
}));

const hasEmptyFields = jest.fn();
jest.mock('@utils/Object', () => ({
    hasEmptyFields: (...a: any[]) => (hasEmptyFields as any)(...a),
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
    useAppDispatch: () => dispatchMock,
}));

const createProductManagementMock = jest.fn();
jest.mock('@redux/product-management/actions', () => ({
    createProductManagement: (...a: any[]) => createProductManagementMock(...a),
}));

describe('CreateProductModal', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        // dispatch simula retorno directo (sincrónico)
        dispatchMock.mockImplementation((a: any) => a);
    });

    const baseProps = () => ({
        toggleModal: jest.fn(),
        toggleToast: jest.fn(),
        handleMessageToast: jest.fn(),
    });

    it('muestra validación cuando faltan campos', async () => {
        hasEmptyFields.mockReturnValue(true);
        render(<CreateProductModal {...baseProps()} />);
        await act(async () => {
            fireEvent.click(screen.getByText('Crear'));
        });
        expect(screen.getByText('*Campos requeridos')).toBeInTheDocument();
        expect(dispatchMock).not.toHaveBeenCalled();
    });

    it('envía y cierra cuando createInput retorna data', async () => {
        hasEmptyFields.mockReturnValue(false);
        createProductManagementMock.mockReturnValue({
            payload: { data: {}, message: 'created!' },
            meta: { requestStatus: 'fulfilled' },
        });
        0;

        const props = baseProps();
        render(<CreateProductModal {...props} />);

        fireEvent.change(screen.getByLabelText('Código del producto'), { target: { value: 'N23' } });
        fireEvent.change(screen.getByLabelText('Descripción del producto'), { target: { value: 'product' } });
        fireEvent.change(screen.getByLabelText('DocumentTYpe'), { target: { value: 'CC' } });
        fireEvent.click(screen.getByTestId('sel-Estado del producto'));
        await act(async () => {
            fireEvent.click(screen.getByText('Crear'));
        });

        expect(createProductManagementMock).toHaveBeenCalled();
        expect(props.toggleModal).toHaveBeenCalled();
    });

    it('muestra mensaje de error de backend si payload es un error', async () => {
        hasEmptyFields.mockReturnValue(false);
        createProductManagementMock.mockReturnValue({
            error: 'invalid data',
            payload: '{"cc": "invalid type", "message": "invalid type"}',
        });
        const props = baseProps();
        render(<CreateProductModal {...props} />);
        fireEvent.change(screen.getByLabelText('Código del producto'), { target: { value: 'N23' } });
        fireEvent.change(screen.getByLabelText('Descripción del producto'), { target: { value: 'product' } });
        fireEvent.change(screen.getByLabelText('DocumentTYpe'), { target: { value: 'CC' } });
        fireEvent.click(screen.getByTestId('sel-Estado del producto'));
        await act(async () => {
            fireEvent.click(screen.getByText('Crear'));
        });
        expect(screen.getByText('*invalid type')).toBeInTheDocument();
        expect(props.toggleModal).not.toHaveBeenCalled();
    });
});
