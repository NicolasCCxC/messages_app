import { render, screen, fireEvent } from '@testing-library/react';
import ProductManagement from './ProductManagement';

const getProductManagementMock = jest.fn();
const modifyProductManagementMock = jest.fn();

jest.mock('@redux/product-management/actions', () => ({
    __esModule: true,
    getProductManagement: (...args: any[]) => getProductManagementMock(...args),
    modifyProductManagement: (...args: any[]) => modifyProductManagementMock(...args),
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
            { code: '1', description: 'test 1', documentType: 'CC', active: false },
            { code: '2', description: 'test 2', documentType: 'CE', active: false },
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

describe('ProductManagement page', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        selectorState = {
            productManagement: {
                data: {
                    totalPages: 1,
                },
                products: [{ label: 'Producto A', value: 'p1' }],
            },
        };
        getProductManagementMock.mockReturnValue(async () => ({ payload: {} }));
        modifyProductManagementMock.mockReturnValue(async () => ({ payload: {} }));
    });

    it('Funcionamiento consultar y crear', async () => {
        render(<ProductManagement />);
        const button = screen.getByText('Consultar');
        fireEvent.click(button);
        expect(getProductManagementMock).toHaveBeenCalledWith({ search: '' });
        const create = screen.getByText('Crear');
        fireEvent.click(create);
        expect(await screen.getByText('Cerrar')).toBeInTheDocument();
    });
});
