/** @jest-environment jsdom */
import { render, screen, fireEvent } from '@testing-library/react';

jest.mock('@components/title', () => ({ Title: ({ title }: any) => <h1>{title}</h1> }));
jest.mock('@components/breadcrumb', () => ({ Breadcrumb: () => <nav /> }));
jest.mock('@components/text-input', () => ({
  TextInput: ({ placeholder, onChange, value }: any) => (
    <input placeholder={placeholder} value={value} onChange={onChange} />
  ),
}));
jest.mock('@components/button', () => ({
  Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button>,
}));
jest.mock('@components/toast', () => ({
  Toast: ({ message, open }: any) => (open ? <div data-testid="toast">{message}</div> : null),
}));
jest.mock('./ExecutingIndexGenerationModal', () => ({
  ExecutingIndexGenerationModal: ({ toggleModal }: any) => (
    <div data-testid="modal">
      Modal
      <button onClick={toggleModal}>close</button>
    </div>
  ),
}));
jest.mock('@components/table', () => ({
  Table: ({ editing, data }: any) => (
    <div data-testid="table">
      pages:{data?.pages ?? 0}
      <button onClick={() => editing?.onPageChange?.(2, 'xxx')}>go-p2</button>
    </div>
  ),
}));

jest.mock('@constants/Paginator', () => ({ __esModule: true, ITEMS_PER_PAGE: 25 }));
jest.mock('@constants/Validation', () => ({ __esModule: true, TIME_TO_GET_DATA: 999999999 }));

jest.mock('@hooks/useTableData', () => ({
  useTableData: () => ({
    onFieldChange: jest.fn(),
    updateData: jest.fn(),
  }),
}));
const displaySearchMessage = jest.fn();
const handleSearchChange = jest.fn();
jest.mock('@hooks/useTableSearch', () => ({
  useTableSearch: () => ({
    displaySearchMessage,
    handleSearchChange,
    searchValue: 'abc',
    showSearchMessage: false,
  }),
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) =>
    sel({
      productManagement: { allProducts: [{ value: 'p1', label: 'P1' }] },
      executingIndexGeneration: { elements: [{ id: '1' }], data: { totalPages: 4 } },
    }),
}));

const getIndex = jest.fn((args: any) => ({ type: 'getIndex', meta: args }));
jest.mock('@redux/executing-index-generation/actions', () => ({
  __esModule: true,
  getIndex: (args: any) => getIndex(args),
}));

jest.mock('.', () => ({
  __esModule: true,
  BREADCRUMB_ITEMS: [],
  TABLE_FIELDS: (all: any[]) => ({ header: [], body: [], all }),
}));

import ExecutingIndexGeneration from './ExecutingIndexGeneration';

describe('ExecutingIndexGeneration page', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('despacha getIndex al montar, busca y paginación via Table', () => {
    render(<ExecutingIndexGeneration />);

    expect(getIndex).toHaveBeenCalledWith({});

    fireEvent.click(screen.getByRole('button', { name: /Consultar/i }));
    expect(displaySearchMessage).toHaveBeenCalled();
    expect(getIndex).toHaveBeenCalledWith({ size: 25, search: 'abc' });

    fireEvent.click(screen.getByRole('button', { name: /Cargar archivo fuente/i }));
    expect(screen.getByTestId('modal')).toBeInTheDocument();

    fireEvent.click(screen.getByText('go-p2'));
    expect(getIndex).toHaveBeenCalledWith({ size: 25, page: 2, search: 'xxx' });
  });
});
