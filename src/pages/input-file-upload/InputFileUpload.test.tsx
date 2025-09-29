/** @jest-environment jsdom */
import { render, screen, fireEvent } from '@testing-library/react';

jest.mock('@components/title', () => ({
  __esModule: true,
  Title: ({ title }: any) => <h1>{title}</h1>,
}));
jest.mock('@components/breadcrumb', () => ({
  __esModule: true,
  Breadcrumb: ({ items }: any) => <nav>{items?.length || 0}</nav>,
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
jest.mock('./CreateFileUploadModal', () => ({
  __esModule: true,
  CreateFileUploadModal: ({ toggleModal }: any) => (
    <div data-testid="modal">
      <button onClick={toggleModal}>close</button>
    </div>
  ),
}));
jest.mock('@components/table', () => ({
  __esModule: true,
  Table: ({ editing, data, search, customIcons }: any) => (
    <div data-testid="table">
      pages:{data?.pages ?? 0} search:{search?.value ?? ''}
      <button onClick={() => editing?.onPageChange?.(5, 'q')}>go-p5</button>
      <div data-testid="icons">{customIcons?.({ id: '1', status: 'ACTIVE' })}</div>
    </div>
  ),
}));

jest.mock('@hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage: jest.fn(),
    handleSearchChange: jest.fn(),
    searchValue: 'abc',
    showSearchMessage: false,
  }),
}));
jest.mock('../../hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage: jest.fn(),
    handleSearchChange: jest.fn(),
    searchValue: 'abc',
    showSearchMessage: false,
  }),
}));

jest.mock('@hooks/useTableData', () => ({
  __esModule: true,
  useTableData: () => ({
    onFieldChange: jest.fn(),
    updateData: jest.fn(),
  }),
}));
jest.mock('../../hooks/useTableData', () => ({
  __esModule: true,
  useTableData: () => ({
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
  TIME_TO_GET_DATA: 999999999,
}));

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  __esModule: true,
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) =>
    sel({
      inputFileUpload: { elements: [{ id: '1', status: 'ACTIVE' }], data: { totalPages: 2 } },
    }),
}));

const getFileMock = jest.fn();
jest.mock('@redux/input-file-upload/actions', () => ({
  __esModule: true,
  getFile: (a?: any) => {
    getFileMock(a);
    return { type: 'getFile', meta: a };
  },
}));

jest.mock('./TableIcons', () => ({
  __esModule: true,
  TableIcons: ({ item }: any) => <span>icons-for-{item?.id}</span>,
}));

import InputFileUpload from './InputFileUpload';

describe('InputFileUpload page', () => {
  beforeEach(() => jest.clearAllMocks());

  it('monta, consulta, busca, pagina y abre modal', () => {
    render(<InputFileUpload />);

    expect(getFileMock).toHaveBeenCalledWith({});

    fireEvent.click(screen.getByText('Consultar'));
    expect(getFileMock).toHaveBeenCalledWith({ size: 10, search: 'abc' });

    fireEvent.click(screen.getByText('Cargar archivo fuente'));
    expect(screen.getByTestId('modal')).toBeInTheDocument();

    fireEvent.click(screen.getByText('go-p5'));
    expect(getFileMock).toHaveBeenCalledWith({ size: 10, page: 5, search: 'q' });

    expect(screen.getByTestId('icons')).toHaveTextContent('icons-for-1');
  });
});
