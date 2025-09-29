/** @jest-environment jsdom */
import { render, screen, fireEvent } from '@testing-library/react';
import ExecutionAssistedProcess from './ExecutionAssistedProcess';
import * as reduxStore from '@redux/store';
import { getAssistedProcess } from '@redux/execution-assisted-process/actions';

jest.mock('@redux/store', () => ({
    __esModule: true,
    useAppDispatch: jest.fn(),
    useAppSelector: jest.fn(),
}));

jest.mock('@redux/execution-assisted-process/actions', () => ({
    __esModule: true,
    getAssistedProcess: jest.fn((args: any) => ({ type: 'getAssisted', meta: args })),
}));

jest.mock('@components/title', () => ({ Title: ({ title }: any) => <h1>{title}</h1> }));
jest.mock('@components/breadcrumb', () => ({ Breadcrumb: () => <nav /> }));
jest.mock('@components/text-input', () => ({
    TextInput: ({ placeholder, onChange, value }: any) => <input placeholder={placeholder} value={value} onChange={onChange} />,
}));
jest.mock('@components/button', () => ({ Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button> }));
jest.mock('@components/toast', () => ({ Toast: ({ open, message }: any) => (open ? <div>{message}</div> : null) }));
jest.mock('./ExecutionAssistedProcessModal', () => ({
    ExecutionAssistedProcessModal: ({ toggleModal }: any) => (
        <div data-testid="modal">
            <button onClick={toggleModal}>close</button>
        </div>
    ),
}));
jest.mock('@components/table', () => ({
    Table: ({ editing, data }: any) => (
        <div data-testid="table">
            pages:{data?.pages ?? 0}
            <button onClick={() => editing?.onPageChange?.(3, 'q')}>go-p3</button>
        </div>
    ),
}));

const displaySearchMessage = jest.fn();
const handleSearchChange = jest.fn();
jest.mock('@hooks/useTableSearch', () => ({
    useTableSearch: () => ({
        displaySearchMessage,
        handleSearchChange,
        searchValue: 'zzz',
        showSearchMessage: false,
    }),
}));
jest.mock('@hooks/useTableData', () => ({
    useTableData: () => ({ onFieldChange: jest.fn(), updateData: jest.fn() }),
}));

jest.mock('.', () => ({ __esModule: true, BREADCRUMB_ITEMS: [], TABLE_FIELDS: () => ({ header: [], body: [], all: [] }) }));

describe('ExecutionAssistedProcess page', () => {
    const mockElements = [{ user: { email: 'prueba' }, product: { code: '23', description: 'prueba' } }];

    beforeEach(() => {
        jest.clearAllMocks();
        jest.useFakeTimers();

        // Mockeamos selector y dispatch
        (reduxStore.useAppSelector as unknown as jest.Mock).mockImplementation(sel =>
            sel({
                productManagement: { allProducts: [{ value: 'P', label: 'Prod' }] },
                executionAssistedProcess: { elements: mockElements, data: { totalPages: 5 } },
            })
        );

        (reduxStore.useAppDispatch as unknown as jest.Mock).mockReturnValue(jest.fn());
    });

    afterEach(() => {
        jest.runOnlyPendingTimers();
        jest.useRealTimers();
    });

    it('despacha al montar, busca y abre modal', () => {
        render(<ExecutionAssistedProcess />);

        // Despacho inicial
        expect(getAssistedProcess).toHaveBeenCalledWith({});

        // Buscar
        fireEvent.click(screen.getByText('Consultar'));
        expect(displaySearchMessage).toHaveBeenCalled();
        expect(getAssistedProcess).toHaveBeenCalledWith({ search: 'zzz' });

        // Abrir modal
        fireEvent.click(screen.getByText('Generar extractos'));
        expect(screen.getByTestId('modal')).toBeInTheDocument();

        // Cambiar página
        fireEvent.click(screen.getByText('go-p3'));
        expect(getAssistedProcess).toHaveBeenCalledWith({ page: 3, search: 'q' });
    });
});
