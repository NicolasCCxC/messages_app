import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import ManageContentProduct from './ManageContentProduct';

const getManageContentProductMock = jest.fn();
const getAllProductsMock = jest.fn();
const deleteContentMock = jest.fn();

jest.mock('@redux/manage-content-product/actions', () => ({
  __esModule: true,
  getManageContentProduct: (...a: any[]) => getManageContentProductMock(...a),
  deleteContentProduct: (...a: any[]) => deleteContentMock(...a),
}));

jest.mock('@redux/product-management/actions', () => ({
  __esModule: true,
  getAllProducts: (...a: any[]) => getAllProductsMock(...a),
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
  useTableData: (rows: any[]) => {
    let current = rows;
    return {
      data: current,
      onFieldChange: jest.fn(),
      updateData: (updater: any) => {
        current = typeof updater === 'function' ? updater(current) : updater;
      },
    };
  },
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
          <button
            aria-label={`trash-${it.id}`}
            onClick={() => editing.onIconClick('trashBlue', it)}
          >
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
    DialogModal: ({ onConfirm, onClose }: any) => (
      <div>
        <button onClick={onConfirm}>confirm</button>
        <button onClick={onClose}>cancel</button>
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
  TextInput: (props: any) => (
    <input aria-label={props.label || 'input'} {...props} />
  ),
}));
jest.mock('@components/button', () => ({
  __esModule: true,
  Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button>,
}));
jest.mock('@components/toast', () => ({
  __esModule: true,
  NotificationType: { Error: 'Error' },
  Toast: ({ message, open }: any) =>
    open ? <div role="alert">{message}</div> : null,
}));

describe('ManageContentProduct page', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    selectorState = {
      productManagement: { allProducts: [{ value: 'p1', label: 'P1' }] },
      manageContentProduct: {
        content: [
          { id: '1', name: 'fila 1', product: 'p1', requiredFields: [] },
          { id: '2', name: 'fila 2', product: 'p1', requiredFields: [] },
        ],
        manageData: { totalPages: 1 },
      },
    };
    getManageContentProductMock.mockReturnValue(async () => ({ payload: {} }));
    getAllProductsMock.mockReturnValue(async () => ({ payload: {} }));
    deleteContentMock.mockImplementation((id: string) => async () => ({
      payload: { message: `deleted ${id}` },
      meta: { requestStatus: 'fulfilled' },
    }));
  });

  it('monta, lista, abre modal y elimina', async () => {
    render(<ManageContentProduct />);

    expect(await screen.findByLabelText('trash-1')).toBeInTheDocument();
    expect(screen.getByLabelText('trash-2')).toBeInTheDocument();

    await act(async () => {
      fireEvent.click(screen.getByLabelText('trash-1'));
    });
    await waitFor(() => expect(screen.getByText('confirm')).toBeInTheDocument());

    await act(async () => {
      fireEvent.click(screen.getByText('confirm'));
    });

    expect(deleteContentMock).toHaveBeenCalledTimes(1);
    expect(deleteContentMock).toHaveBeenCalledWith('1');

    await waitFor(() =>
      expect(screen.getByRole('alert')).toHaveTextContent('deleted 1')
    );
  });
});
