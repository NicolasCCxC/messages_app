/* eslint-disable @typescript-eslint/no-explicit-any */
import { render, screen, fireEvent, act } from '@testing-library/react';
import { CreateDataModal } from './CreateDataModal';

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
    SelectSearch: ({ onChangeOption, label, value, error }: any) => (
        <div>
            <span>{label}</span>
            <button data-testid={`select-${label}`} onClick={() => onChangeOption({ label: 'Opt', value: 'v' })}>
                SELECT-{label}
            </button>
            <span data-testid={`val-${label}`}>{value}</span>
            {error && <i data-testid={`err-${label}`}>err</i>}
        </div>
    ),
}));

jest.mock('@components/text-input', () => ({
    TextInput: ({ label, name, value, onChange }: any) => (
        <label>
            {label}
            <input aria-label={label} name={name} value={value} onChange={e => onChange(e as any)} />
        </label>
    ),
}));

jest.mock('@constants/DefaultPlaceholder', () => ({ DEFAULT_PLACEHOLDER: 'PH' }));
jest.mock('@constants/DefaultSelectOptions', () => ({ DEFAULT_STATE_OPTIONS: [{ label: 'Activo', value: true }] }));
jest.mock('@constants/Validation', () => ({ REQUIRED_FIELDS: 'Campos obligatorios' }));

const hasEmptyFieldsMock = jest.fn();
jest.mock('@utils/Array', () => ({
    hasEmptyFields: (...a: any[]) => (hasEmptyFieldsMock as any)(...a),
}));

describe('CreateDataModal', () => {
    beforeEach(() => jest.clearAllMocks());

    const baseProps = () => ({
        toggleModal: jest.fn(),
        toggleToast: jest.fn(),
        handleMessageToast: jest.fn(),
        products: [{ label: 'P1', value: 'p1' }],
        createData: jest.fn(),
    });

    it('muestra validación y NO crea cuando hasEmptyFields=true', async () => {
        hasEmptyFieldsMock.mockReturnValue(true);
        const props = baseProps();

        render(<CreateDataModal {...props} />);

        await act(async () => {
            fireEvent.click(screen.getByText('SAVE'));
        });

        expect(props.createData).not.toHaveBeenCalled();
        expect(screen.getByText('*Campos obligatorios')).toBeInTheDocument();
    });

    it('crea cuando hasEmptyFields=false y flujo fulfilled', async () => {
        hasEmptyFieldsMock.mockReturnValue(false);
        const props = baseProps();
        (props.createData as jest.Mock).mockResolvedValue({
            meta: { requestStatus: 'fulfilled' },
            payload: { message: 'ok!!' },
        });

        render(<CreateDataModal {...props} />);

        fireEvent.click(screen.getByTestId('select-Producto'));
        fireEvent.click(screen.getByTestId('select-Estado'));
        fireEvent.change(screen.getByLabelText('Ruta archivo entrada'), { target: { value: 'in' } });
        fireEvent.change(screen.getByLabelText('Ruta archivo procesados'), { target: { value: 'out' } });

        await act(async () => {
            fireEvent.click(screen.getByText('SAVE'));
        });

        expect(props.createData).toHaveBeenCalled();
        expect(props.handleMessageToast).toHaveBeenCalledWith('ok!!');
        expect(props.toggleToast).toHaveBeenCalled();
        expect(props.toggleModal).toHaveBeenCalled();
    });
});
