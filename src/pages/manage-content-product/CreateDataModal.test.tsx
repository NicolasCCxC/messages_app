import { render, screen, waitFor, act } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { CreateDataModal } from './CreateDataModal';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { ReduxResponse } from '@constants/ReduxResponse';
import { REQUIRED_FIELDS } from '@constants/Validation';
import { RequiredFields } from './RequiredFields';

jest.mock('@redux/store');

const mockedUseAppDispatch = useAppDispatch as unknown as jest.Mock;
const mockedUseAppSelector = useAppSelector as unknown as jest.Mock;

jest.mock('@components/modal', () => ({
    Modal: jest.fn(({ children, onSave, title }) => (
        <div data-testid="mock-modal">
            <h1>{title}</h1>
            {children}
            <button onClick={onSave}>Save</button>
        </div>
    )),
}));
jest.mock('@components/select-search', () => ({
    SelectSearch: jest.fn(({ onChangeOption, value }) => (
        <button data-testid="mock-select" onClick={() => onChangeOption({ value: 'prod-2', label: 'Product 2' })}>
            {value || 'Select Product'}
        </button>
    )),
}));
jest.mock('@components/text-input', () => ({
    TextInput: jest.fn(({ onChange, value }) => (
        <input data-testid="mock-text-input" type="text" value={value || ''} onChange={e => onChange(e)} />
    )),
}));

jest.mock('./RequiredFields', () => ({
    RequiredFields: jest.fn((props) => <div data-testid="mock-required-fields" {...props} />),
}));

describe('CreateDataModal Component', () => {
    const mockDispatch = jest.fn();
    const mockToggleModal = jest.fn();
    const mockToggleToast = jest.fn();
    const mockHandleMessageToast = jest.fn();
    const mockHandleUpdateData = jest.fn();
    const mockProducts = [{ value: 'prod-1', label: 'Product 1' }];
    const mockReduxState = {
        productInput: { allInputs: [] },
        manageContentProduct: { content: [] },
    };

    beforeEach(() => {
        jest.clearAllMocks();
        mockedUseAppDispatch.mockReturnValue(mockDispatch);
        mockedUseAppSelector.mockImplementation(selector => selector(mockReduxState));
    });

    it('debería renderizar en modo "Crear" y mostrar error si los campos están vacíos', async () => {
        const user = userEvent.setup();
        render(
            <CreateDataModal isModify={false} products={mockProducts} toggleModal={mockToggleModal} toggleToast={mockToggleToast} handleMessageToast={mockHandleMessageToast} handleUpdateData={mockHandleUpdateData} modifyData={{}} />
        );
        
        await user.click(screen.getByText('Save'));
        expect(await screen.findByText(`*${REQUIRED_FIELDS}`)).toBeInTheDocument();
        expect(mockDispatch).not.toHaveBeenCalled();
    });

    it('debería renderizar en modo "Modificar" y despachar la carga de datos inicial', () => {
        const modifyData = { product: 'prod-1', typeFile: 'CSV', nameIndexFile: 'test-index', requiredFields: [] };
        render(
            <CreateDataModal isModify={true} products={mockProducts} toggleModal={mockToggleModal} toggleToast={mockToggleToast} handleMessageToast={mockHandleMessageToast} handleUpdateData={mockHandleUpdateData} modifyData={modifyData} />
        );
        expect(mockDispatch).toHaveBeenCalled();
    });

    it('debería despachar la acción de crear y cerrar el modal en caso de éxito', async () => {
        const user = userEvent.setup();
        mockDispatch.mockResolvedValue({ 
            meta: { requestStatus: ReduxResponse.Fulfilled },
            payload: { message: 'Success!' }
        });

        render(
            <CreateDataModal isModify={false} products={mockProducts} toggleModal={mockToggleModal} toggleToast={mockToggleToast} handleMessageToast={mockHandleMessageToast} handleUpdateData={mockHandleUpdateData} modifyData={{}} />
        );

        const selectButtons = screen.getAllByTestId('mock-select');
        await user.click(selectButtons[0]);
        await user.click(selectButtons[1]);
        await user.type(screen.getByTestId('mock-text-input'), 'my-index-file');
        
        const requiredFieldsProps = (RequiredFields as jest.Mock).mock.calls[0][0];
        act(() => {
            requiredFieldsProps.updateField(0, { isFixed: true, content: 'some-content' });
        });

        await user.click(screen.getByText('Save'));

        expect(mockDispatch).toHaveBeenCalled();

        await waitFor(() => {
            expect(mockHandleMessageToast).toHaveBeenCalledWith('Success!');
        });
        expect(mockToggleToast).toHaveBeenCalledTimes(1);
        expect(mockToggleModal).toHaveBeenCalledTimes(1);
    });
});