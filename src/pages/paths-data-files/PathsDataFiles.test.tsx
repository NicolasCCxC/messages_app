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
jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ placeholder, value, onChange }: any) => (
    <input placeholder={placeholder} value={value} onChange={onChange} />
  ),
}));
jest.mock('@components/button', () => ({
  __esModule: true,
  Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button>,
}));
jest.mock('@components/toast', () => ({
  __esModule: true,
  NotificationType: { Error: 'error' },
  Toast: ({ open, message }: any) => (open ? <div data-testid="toast">{message}</div> : null),
}));
jest.mock('@components/table', () => ({
  __esModule: true,
  Table: ({ editing, search, data }: any) => (
    <div data-testid="table">
      pages:{data?.pages ?? 0} search:{search?.value ?? ''}
      <button onClick={() => editing?.onPageChange?.(4, 's')}>go-4</button>
    </div>
  ),
}));


jest.mock('./CreateDataModal', () => ({
  __esModule: true,
  CreateDataModal: ({ toggleModal, handleMessageToast, toggleToast }: any) => (
    <div data-testid="modal">
      <button
        onClick={() => {
          handleMessageToast('OK-MODAL');
          toggleToast();
          toggleModal();
        }}
      >
        crear-ok
      </button>
    </div>
  ),
}));


jest.mock('@hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage: jest.fn(),
    handleSearchChange: jest.fn(),
    searchValue: 'q',
    showSearchMessage: false,
  }),
}));
jest.mock('../../hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage: jest.fn(),
    handleSearchChange: jest.fn(),
    searchValue: 'q',
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


jest.mock('@constants/Paginator', () => ({
  __esModule: true,
  ITEMS_PER_PAGE: 10,
}));
jest.mock('@constants/Validation', () => ({
  __esModule: true,
  REQUIRED_FIELDS: 'Faltan',
}));
jest.mock('.', () => ({
  __esModule: true,
  BREADCRUMB_ITEMS: [{ label: 'X' }],
  DEFAULT_FORM_VALUES: {},
  TABLE_FIELDS: () => ({ header: [], body: [] }),
}));


const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) =>
    sel({
      pathsDataFiles: { data: { totalPages: 3 }, paths: [{ id: '1' }] },
      productManagement: { allProducts: [{ id: 'p1', name: 'P1' }] },
    }),
}));


const getPathsMock = jest.fn();
const getProductsMock = jest.fn();

jest.mock('@redux/paths-data-files/actions', () => ({
  __esModule: true,
  getPathsDataFile: (a?: any) => {
    getPathsMock(a);
    return { type: 'getPathsDataFile', meta: a };
  },
  createPathDataFile: (a?: any) => ({ type: 'createPathDataFile', meta: a }),
  modifyPathDataFile: (a?: any) => ({ type: 'modifyPathDataFile', meta: a }),
}));
jest.mock('@redux/product-management/actions', () => ({
  __esModule: true,
  getAllProducts: (...args: any[]) => {
    getProductsMock(...args);
    return { type: 'getAllProducts', meta: args };
  },
}));

import PathsDataFiles from './PathsDataFiles';

describe('PathsDataFiles page', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    dispatchMock.mockImplementation((action) => action);
  });

  it('monta, busca, pagina y abre modal', () => {
    render(<PathsDataFiles />);

    expect(getPathsMock).toHaveBeenCalledWith({ size: 10 });
    expect(getProductsMock).toHaveBeenCalled();


    fireEvent.click(screen.getByText('Consultar'));
    expect(getPathsMock).toHaveBeenCalledWith({ search: 'q' });


    fireEvent.click(screen.getByText('go-4'));
    expect(getPathsMock).toHaveBeenCalledWith({ size: 10, page: 4, search: 's' });


    fireEvent.click(screen.getByText('Crear'));
    expect(screen.getByTestId('modal')).toBeInTheDocument();


    fireEvent.click(screen.getByText('crear-ok'));
    expect(screen.getByTestId('toast')).toHaveTextContent('OK-MODAL');
  });
});
