/** @jest-environment jsdom */
import { render, screen, fireEvent } from '@testing-library/react';

jest.mock('@components/title', () => ({
  __esModule: true,
  Title: ({ title }: any) => <h1>{title}</h1>,
}));
jest.mock('@components/breadcrumb', () => ({
  __esModule: true,
  Breadcrumb: ({ items }: any) => <nav data-testid="bc">{items?.length || 0}</nav>,
}));
jest.mock('@components/form', () => ({
  __esModule: true,
  Form: ({ children, className }: any) => (
    <form className={className} onSubmit={(e) => e.preventDefault()}>
      {children}
    </form>
  ),
}));
jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ placeholder, value, onChange, isSearch }: any) => (
    <input placeholder={placeholder} value={value} onChange={onChange} data-search={!!isSearch} />
  ),
}));
jest.mock('@components/button', () => ({
  __esModule: true,
  Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button>,
}));
jest.mock('@components/table', () => ({
  __esModule: true,
  Table: ({ data, search }: any) => (
    <div data-testid="table">
      pages:{data?.pages ?? 0} search:{search?.value ?? ''}
    </div>
  ),
}));
jest.mock('@components/toast', () => ({
  __esModule: true,
  NotificationType: { Error: 'error' },
  Toast: ({ open, message }: any) => (open ? <div data-testid="toast">{message}</div> : null),
}));

jest.mock('@hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage: jest.fn(),
    handleSearchChange: jest.fn(),
    searchValue: 'prod',
    showSearchMessage: false,
  }),
}));
jest.mock('../../hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage: jest.fn(),
    handleSearchChange: jest.fn(),
    searchValue: 'prod',
    showSearchMessage: false,
  }),
}));

jest.mock('@hooks/useTableData', () => ({
  __esModule: true,
  useTableData: (paths: any[]) => ({
    data: paths,
    onFieldChange: jest.fn(),
    updateData: jest.fn(),
  }),
}));
jest.mock('../../hooks/useTableData', () => ({
  __esModule: true,
  useTableData: (paths: any[]) => ({
    data: paths,
    onFieldChange: jest.fn(),
    updateData: jest.fn(),
  }),
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) =>
    sel({
      productManagement: { allProducts: [{ id: 'p1', name: 'P1' }, { id: 'p2', name: 'P2' }] },
      paths: { paths: [{ id: 'x', productId: 'p1', routeOutputExtract: '/a', routeOutputIndex: '/b' }], pages: 3 },
    }),
}));

const getAllProductsMock = jest.fn();
const getExitPathsMock = jest.fn();

jest.mock('@redux/product-management/actions', () => ({
  __esModule: true,
  getAllProducts: (...args: any[]) => {
    getAllProductsMock(...args);
    return { type: 'getAllProducts', meta: args };
  },
}));

jest.mock('@redux/paths/actions', () => ({
  __esModule: true,
  getExitPaths: (a?: any) => {
    getExitPathsMock(a);
    return { type: 'getExitPaths', meta: a };
  },
  deletePath: (id: string) => ({ type: 'deletePath', meta: id }),
  updatePath: (item: any) => ({ type: 'updatePath', meta: item }),
}));

jest.mock('.', () => ({
  __esModule: true,
  REQUIRED_FIELDS: ['productId', 'routeOutputExtract', 'routeOutputIndex'],
  ROUTES: [{ label: 'Home' }],
  getTableFields: () => ({ header: [], body: [] }),
  CreateRecordModal: ({ toggleModal, updateNotification }: any) => (
    <div data-testid="modal">
      <button onClick={() => updateNotification('OK-CREADO')}>simular-ok</button>
      <button onClick={toggleModal}>cerrar</button>
    </div>
  ),
}));

import ExitPaths from './ExitPaths';

describe('ExitPaths page', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('carga productos/paths al montar; busca y abre modal', () => {
    render(<ExitPaths />);

    expect(getAllProductsMock).toHaveBeenCalled();
    expect(getExitPathsMock).toHaveBeenCalledWith({ page: 0 });
    fireEvent.click(screen.getByText('Consultar'));
    expect(getExitPathsMock).toHaveBeenCalledWith({ search: 'prod' });
    fireEvent.click(screen.getByText('Crear'));
    expect(screen.getByTestId('modal')).toBeInTheDocument();

    fireEvent.click(screen.getByText('simular-ok'));
    expect(screen.getByTestId('toast')).toHaveTextContent('OK-CREADO');
  });
});
