import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import ProductInput from './ProductInput';

const getAllProductsMock = jest.fn();
const getInputsMock = jest.fn();
const deleteInputMock = jest.fn();
const updateInputMock = jest.fn();

jest.mock('@redux/product-management/actions', () => ({
    __esModule: true,
    getAllProducts: (...args: any[]) => getAllProductsMock(...args),
}));

jest.mock('@redux/product-input/actions', () => ({
    __esModule: true,
    getInputs: (...args: any[]) => getInputsMock(...args),
    deleteInput: (...args: any[]) => deleteInputMock(...args),
    updateInput: (...args: any[]) => updateInputMock(...args),
}));

const dispatchMock: any = jest.fn(async (thunk: any) => {
    if (typeof thunk === 'function') {
        return await thunk(dispatchMock, () => ({}), undefined);
    }
    return thunk;
});

let selectorState: any;

jest.mock('@redux/store', () => ({
    __esModule: true,
    useAppDispatch: () => dispatchMock,
    useAppSelector: (sel: any) => sel(selectorState),
}));

jest.mock('@hooks/useTableData', () => ({
    __esModule: true,
    useTableData: () => ({
        data: [
            { id: '1', name: 'test 1' },
            { id: '2', name: 'test 2' },
        ],
        onFieldChange: jest.fn(),
        updateData: jest.fn(),
    }),
}));

jest.mock('@hooks/useTableSearch', () => ({
    __esModule: true,
    useTableSearch: () => {
        const displaySearchMessage = jest.fn();
        const handleSearchChange = jest.fn();
        return {
            displaySearchMessage,
            handleSearchChange,
            searchValue: '',
            showSearchMessage: false,
        };
    },
}));

jest.mock('@components/table', () => ({
    __esModule: true,
    Table: ({ data, editing }: any) => (
        <div data-testid="table">
            {data.current.map((it: any) => (
                <div key={it.id}>
                    <span>{it.name}</span>
                    <button aria-label={`trash-${it.id}`} onClick={() => editing.onDeleteRow(it.id)}>
                        trash
                    </button>
                </div>
            ))}
        </div>
    ),
}));

jest.mock('@components/modal', () => {
    const real = jest.requireActual('@components/modal');
    return {
        __esModule: true,
        ...real,
        CreateRecordModal: ({ toggleModal }: any) => (
            <div>
                <button onClick={toggleModal}>close</button>
            </div>
        ),
    };
});

jest.mock('@components/breadcrumb', () => ({
    __esModule: true,
    Breadcrumb: ({ className }: any) => <div className={className}>breadcrumb</div>,
}));
jest.mock('@components/title', () => ({
    __esModule: true,
    Title: ({ title }: any) => <h1>{title}</h1>,
}));
jest.mock('@components/text-input', () => ({
    __esModule: true,
    TextInput: (props: any) => <input aria-label={props.label || 'input'} {...props} />,
}));
jest.mock('@components/button', () => ({
    __esModule: true,
    Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button>,
}));
jest.mock('@components/toast', () => ({
    __esModule: true,
    NotificationType: { Error: 'Error' },
    Toast: ({ message, open }: any) => (open ? <div role="alert">{message}</div> : null),
}));

describe('ProductInput page', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        selectorState = {
            productInput: {
                inputs: [],
                pages: 1,
            },
            productManagement: {
                allProducts: [{ label: 'Producto A', value: 'p1' }],
            },
        };
        getAllProductsMock.mockReturnValue(async () => ({ payload: {} }));
        getInputsMock.mockReturnValue(async () => ({ payload: {} }));
        deleteInputMock.mockImplementation((id: string) => async () => ({
            payload: { message: `deleted ${id}` },
            meta: { requestStatus: 'fulfilled' },
        }));
        updateInputMock.mockReturnValue(async () => ({ payload: {} }));
    });

    it('renderiza, elimina y muestra notificación', async () => {
        render(<ProductInput />);

        expect(await screen.findByLabelText('trash-1')).toBeInTheDocument();
        expect(screen.getByLabelText('trash-2')).toBeInTheDocument();

        await act(async () => {
            fireEvent.click(screen.getByLabelText('trash-1'));
        });

        expect(deleteInputMock).toHaveBeenCalledWith('1');
        expect(deleteInputMock).toHaveBeenCalledTimes(1);

        await waitFor(() => expect(screen.getByRole('alert')).toHaveTextContent('deleted 1'));
    });
});
