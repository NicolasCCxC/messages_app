/** @jest-environment jsdom */
import { render, screen, fireEvent, waitFor } from '@testing-library/react';

jest.mock('@components/title', () => ({
  __esModule: true,
  Title: ({ title }: any) => <h1>{title}</h1>,
}));

jest.mock('@components/breadcrumb', () => ({
  __esModule: true,
  Breadcrumb: ({ items }: any) => <nav data-testid="breadcrumb">{items?.length || 0}</nav>,
}));

jest.mock('@components/text-input', () => ({
  __esModule: true,
  TextInput: ({ placeholder, onChange, value }: any) => (
    <input placeholder={placeholder} value={value} onChange={onChange} />
  ),
}));

jest.mock('@components/button', () => ({
  __esModule: true,
  Button: ({ text, onClick }: any) => <button onClick={onClick}>{text}</button>,
}));

jest.mock('@components/icon', () => ({
  __esModule: true,
  Icon: ({ alt, onClick }: any) => <button aria-label={alt} onClick={onClick} />,
}));

jest.mock('@components/table', () => ({
  __esModule: true,
  Table: ({ data, editing, search }: any) => (
    <div data-testid="table">
      table pages:{data?.pages ?? 0} search:{search?.value ?? ''}
      <button onClick={() => editing?.onPageChange?.(2, 'foo')}>goto-page-2</button>
    </div>
  ),
}));

const displaySearchMessage = jest.fn();
const handleSearchChange = jest.fn();
jest.mock('@hooks/useTableSearch', () => ({
  __esModule: true,
  useTableSearch: () => ({
    displaySearchMessage,
    handleSearchChange,
    searchValue: 'buscar',
    showSearchMessage: false,
  }),
}));

jest.mock('@hooks/useTableData', () => ({
  __esModule: true,
  useTableData: (all: any[]) => ({
    data: all,
    onFieldChange: jest.fn(),
    updateData: jest.fn(),
  }),
}));

const exportPdfMock = jest.fn();
const exportCsvMock = jest.fn();
jest.mock('@utils/DownloadDataTale', () => ({
  __esModule: true,
  handleExportPDF: (...args: any[]) => exportPdfMock(...args),
  handleExportCSV: (...args: any[]) => exportCsvMock(...args),
}));

const apiGetAuditConsultation = jest.fn();
jest.mock('@api/AuditConsultation', () => ({
  __esModule: true,
  apiGetAuditConsultation: (...args: any[]) => apiGetAuditConsultation(...args),
}));

jest.mock('@api/Urls', () => ({
  __esModule: true,
  urls: {
    auditConsultation: {
      get: ({ search, page }: any) => `/api/audits?page=${page ?? 0}&search=${search ?? ''}`,
    },
  },
}));

jest.mock('@models/Request', () => ({
  __esModule: true,
  FetchRequest: function (this: any, url: string) {
    this.url = url;
  },
}));

jest.mock('.', () => ({
  __esModule: true,
  BREADCRUMB_ITEMS: [{ label: 'Inicio' }],
  TABLE_FIELDS: {
    header: [{ value: 'User' }],
    body: [{ name: 'userName' }],
  },
  parseHtmlEntitiesToJson: jest.fn((x) => x), // mock para evitar error
}));

import AuditConsultation from './AuditConsultation';

describe('AuditConsultation page', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    apiGetAuditConsultation.mockResolvedValue({
      data: {
        totalPages: 3,
        content: [
          { id: '1', user: { name: 'Juan' }, ip: '1.1.1.1' },
          { id: '2', user: { name: 'Ana' }, ip: '2.2.2.2' },
        ],
      },
    });
  });

  it('carga datos al montar y muestra la tabla', async () => {
    render(<AuditConsultation />);
    await waitFor(() => expect(apiGetAuditConsultation).toHaveBeenCalledTimes(1));
    await waitFor(() => expect(screen.getByTestId('table')).toBeInTheDocument());
  });

  it('permite exportar PDF y CSV', async () => {
    render(<AuditConsultation />);
    await waitFor(() => expect(apiGetAuditConsultation).toHaveBeenCalledTimes(1));

    fireEvent.click(screen.getByRole('button', { name: /Exportar a PDF/i }));
    expect(exportPdfMock).toHaveBeenCalled();

    fireEvent.click(screen.getByRole('button', { name: /Exportar a CSV/i }));
    expect(exportCsvMock).toHaveBeenCalled();
  });

  it('permite buscar y llama displaySearchMessage', async () => {
    render(<AuditConsultation />);
    await waitFor(() => expect(apiGetAuditConsultation).toHaveBeenCalledTimes(1));

    fireEvent.click(screen.getByRole('button', { name: /Consultar/i }));
    expect(displaySearchMessage).toHaveBeenCalled();
    await waitFor(() => expect(apiGetAuditConsultation).toHaveBeenCalledTimes(2));
  });
});
