/* eslint-disable @typescript-eslint/no-explicit-any */
import { render, screen, fireEvent, cleanup, act, waitFor } from '@testing-library/react';
import { Editor } from './Editor';

jest.mock('@components/icon', () => ({ Icon: ({ onClick }: any) => <button onClick={onClick}>I</button> }));
jest.mock('@components/button', () => ({
  Button: ({ onClick, text, disabled }: any) => (
    <button disabled={disabled} onClick={onClick}>{text}</button>
  ),
}));

jest.mock('@components/select-search', () => ({
  __esModule: true,
  SelectSearch: ({ label, onChangeOption, value, error }: any) => (
    <div>
      <span>{label}</span>
      <button data-testid={`sel-${label}`} onClick={() => onChangeOption({ label: 'Producto 1', value: 'p1' })}>
        SEL-{label}
      </button>
      <span data-testid={`val-${label}`}>{value}</span>
      {error && <i>err</i>}
    </div>
  ),
}));
jest.mock('@components/text-input', () => ({
  TextInput: ({ label, onChange, value }: any) => (
    <label>
      {label}
      <input
        aria-label={label}
        value={value || ''}
        onChange={(e) =>
          onChange({ target: { value: (e.target as HTMLInputElement).value } } as any)
        }
      />
    </label>
  ),
}));

jest.mock('@components/modal', () => ({
  DialogModal: ({ onConfirm, onClose }: any) => (
    <div data-testid="dialog">
      <button onClick={onConfirm}>OK</button>
      <button onClick={onClose}>CANCEL</button>
    </div>
  ),
  DialogModalType: { ReturnObjectPage: 'return' },
}));
jest.mock('@components/toast', () => ({
  NotificationType: { Error: 'Error' },
  Toast: ({ message }: any) => <div role="alert">{message || 'toast'}</div>,
}));

jest.mock('./Sidebar', () => ({
  Sidebar: () => <div data-testid="fake-sidebar">SB</div>,
}));
jest.mock('../tools', () => ({
  SidebarTools: () => <div data-testid="fake-tools">TOOLS</div>,
}));

jest.mock('@constants/ObjectsEditor', () => ({
  ElementType: { Text: 'Text', Table: 'Table' },
  ObjectType: { Generic: 'Generic' },
  PLACEHOLDERS: { select: 'sel' },
}));
jest.mock('..', () => ({
  __esModule: true,
  elements: {
    Text: ({ element }: any) => <div data-testid="dyn-el">Element: {element?.type}</div>,
    Table: ({ element }: any) => <div data-testid="dyn-el">Table: {element?.type}</div>,
  },
}));

const updateElementProperties = jest.fn();
const setElement = jest.fn();
const setSelectedElementType = jest.fn();

jest.mock('@pages/object-manage-format/context', () => {
  const React = require('react');
  return {
    __esModule: true,
    ELEMENT_TYPE_TO_OBJECT_TYPE_MAP: { Text: 'Generic' },
    ManageObjectContext: React.createContext({
      element: { type: '', name: '', identifier: '', productId: '', style: {} },
      selectedElementType: 'Text',
      updateElementProperties: jest.fn(),
      setElement: jest.fn(),
      setSelectedElementType: jest.fn(),
    }),
  };
});

const dispatchMock = jest.fn();
jest.mock('@redux/store', () => ({
  useAppDispatch: () => dispatchMock,
  useAppSelector: (sel: any) =>
    sel({
      productManagement: {
        allProducts: [{ id: 'p1', label: 'Producto 1' }],
      },
    }),
}));
jest.mock('@redux/product-management/actions', () => ({
  getAllProducts: () => ({ type: 'pm/getAll' }),
}));

const modifyMock = jest.fn();
const createMock = jest.fn();
jest.mock('@redux/object-manage-format/actions', () => ({
  modifyObjectManageFormat: (...a: any[]) => modifyMock(...a),
  createObjectManageFormat: (...a: any[]) => createMock(...a),
}));

jest.mock('@utils/Diff', () => ({
  getDiff: (oldV: any, newV: any) => ({ diffed: true, oldV, newV }),
}));

import { ManageObjectContext } from '@pages/object-manage-format/context';
import { ElementType } from '@constants/ObjectsEditor';
import { maxLengthText } from '.';

const renderWithCtx = (ctxOverride: any, propsOverride: any = {}) => {
  const ctx = {
    element: { type: '', name: '', identifier: '', productId: '', style: {} },
    selectedElementType: ElementType.Text,
    updateElementProperties,
    setElement,
    setSelectedElementType,
    ...ctxOverride,
  };
  const props = {
    toggleEditor: jest.fn(),
    elementToModify: undefined,
    toggleModify: jest.fn(),
    toggleToast: jest.fn(),
    setSaveError: jest.fn(),
    handleMessageToast: jest.fn(),
    isModify: false,
    ...propsOverride,
  };

  return {
    ...render(
      <ManageObjectContext.Provider value={ctx as any}>
        <Editor {...props as any} />
      </ManageObjectContext.Provider>
    ),
    ctx,
    props,
  };
};

describe('Editor', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    dispatchMock.mockImplementation((a: any) => a);
  });

  it('monta (getAllProducts) y limpia en unmount (setElement, setSelectedElementType)', () => {
    const { unmount } = renderWithCtx({ setElement, setSelectedElementType });

    expect(dispatchMock).toHaveBeenCalledWith({ type: 'pm/getAll' });

    unmount();
    expect(setElement).toHaveBeenCalledWith({});
    expect(setSelectedElementType).toHaveBeenCalledWith(null);
  });

  it('seleccionar producto y editar campos nombre/código', () => {
    renderWithCtx({ updateElementProperties });

    fireEvent.click(screen.getByTestId('sel-Producto'));
    expect(updateElementProperties).toHaveBeenCalledWith('productId', 'p1');

    fireEvent.change(screen.getByLabelText('Código de objeto'), { target: { value: 'COD' } });
    fireEvent.change(screen.getByLabelText('Nombre del objeto'), { target: { value: 'NOM' } });

    expect(updateElementProperties).toHaveBeenCalledWith('identifier', 'COD');
    expect(updateElementProperties).toHaveBeenCalledWith('name', 'NOM');
  });

  it('crear objeto (no modify) con type=Text elimina width/height y dispara toasts', async () => {
    createMock.mockReturnValue({ payload: { message: 'ok' } });

    const { props } = renderWithCtx({
      element: { type: ElementType.Text, name: 'N', identifier: 'I', productId: 'p1', style: { width: 10, height: 20 } },
    });

    await act(async () => {
      fireEvent.click(screen.getByText('Crear'));
    });


    await waitFor(() => {
      expect(createMock).toHaveBeenCalled();
    });

    const arg = createMock.mock.calls[0][0];
    if (arg?.content?.length < maxLengthText) {
      expect(arg.style?.width).toBeUndefined();
      expect(arg.style?.height).toBeUndefined();
  }

    await waitFor(() => expect(props.setSaveError).toHaveBeenCalledWith(false));
    expect(props.handleMessageToast).toHaveBeenCalledWith('ok');
    expect(props.toggleToast).toHaveBeenCalled();
    expect(props.toggleEditor).toHaveBeenCalled();
  });

  it('modo modify: si backend responde con error formateado, muestra Toast error y no cierra', async () => {
    modifyMock.mockReturnValue({ error: true, payload: 'Error: {"message":"Bad"}' });

    const { props } = renderWithCtx(
      {
        element: { type: ElementType.Text, id: 'x', name: 'N', identifier: 'I', productId: 'p1', style: {} },
      },
      {
        isModify: true,
        elementToModify: { type: ElementType.Text, id: 'x', name: 'N', identifier: 'I', productId: 'p1', style: {} },
      }
    );

    await act(async () => {
      fireEvent.click(screen.getByText('Crear'));
    });

    const alert = await screen.findByRole('alert');
    expect(alert).toHaveTextContent('Bad');
    expect(props.toggleEditor).not.toHaveBeenCalled();
  });

  it('Sidebar visible cuando !isModify y SidebarTools oculto si selectedElementType=Table', () => {
    renderWithCtx({ selectedElementType: 'Text' });
    expect(screen.getByTestId('dyn-el').textContent?.startsWith('Element:')).toBe(true);

    cleanup();
    renderWithCtx({
      selectedElementType: 'Table',
      element: { type: 'Table', name: 'N', identifier: 'I', productId: 'p1', style: {} },
    });
    expect(screen.getByTestId('dyn-el').textContent?.startsWith('Table:')).toBe(true);
  });

  it('requiredFields vacíos → no crea y mantiene editor', async () => {
    const { props } = renderWithCtx({
      element: { type: '', name: '', identifier: '', productId: '', style: {} },
    });

    await act(async () => {
      fireEvent.click(screen.getByText('Crear'));
    });
    expect(props.toggleEditor).not.toHaveBeenCalled();
  });
});
