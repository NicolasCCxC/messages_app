/* eslint-disable @typescript-eslint/no-explicit-any */
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import QueryingHistoricalProcesses from './QueryingHistoricalProcesses';

jest.mock('@redux/store', () => ({
    useAppSelector: (sel: any) => sel({ productManagement: { allProducts: [{ id: 'p1', label: 'P1 X', value: 'p1' }] } }),
}));

jest.mock('@api/QueryingHistoricalProcesses', () => ({
    apiGetHistoricalProcess: jest.fn().mockResolvedValue({
        data: {
            totalPages: 2,
            content: [
                {
                    id: '1',
                    user: { email: 'user@example.com', name: 'U' },
                    product: { id: 'p1', code: 'P001', description: 'Producto 1' },
                    qty: 3,
                },
            ],
        },
    }),
}));

jest.mock('@models/Request', () => ({
    FetchRequest: class {
        constructor(public r: any) {}
    },
}));

jest.mock('@api/Urls', () => ({
    urls: { queryingHistoricalProcesses: { get: (p: any) => `/core/extract?${JSON.stringify(p)}` } },
}));

jest.mock('@hooks/useTableData', () => ({
    useTableData: () => ({ onFieldChange: jest.fn(), updateData: jest.fn() }),
}));

const displaySearchMessage = jest.fn();
const handleSearchChange = jest.fn();
jest.mock('@hooks/useTableSearch', () => ({
    useTableSearch: () => ({
        displaySearchMessage,
        handleSearchChange,
        searchValue: 'term',
        showSearchMessage: false,
    }),
}));

const handleExportCSV = jest.fn();
const handleExportPDF = jest.fn();
jest.mock('@utils/DownloadDataTale', () => ({
    handleExportCSV: (...args: any[]) => handleExportCSV(...args),
    handleExportPDF: () => handleExportPDF(),
}));

jest.mock('@components/breadcrumb', () => ({ Breadcrumb: () => <div /> }));
jest.mock('@components/title', () => ({ Title: () => <h1>Title</h1> }));
jest.mock('@components/text-input', () => ({
    TextInput: ({ onChange, value }: any) => <input aria-label="search" value={value} onChange={onChange} />,
}));
jest.mock('@components/button', () => ({ Button: ({ onClick, text }: any) => <button onClick={onClick}>{text}</button> }));
jest.mock('@components/table', () => ({ Table: () => <div data-testid="table">TABLE</div> }));
jest.mock('@components/icon', () => ({ Icon: ({ onClick, alt }: any) => <button onClick={onClick}>{alt}</button> }));

describe('QueryingHistoricalProcesses', () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('carga datos al montar, permite consultar y exportar', async () => {
        render(<QueryingHistoricalProcesses />);

        // Verifica que la tabla se renderiza con los datos iniciales
        await waitFor(() => expect(screen.getByTestId('table')).toBeInTheDocument());

        // Acción de búsqueda
        fireEvent.click(screen.getByText('Consultar'));
        await waitFor(() => expect(displaySearchMessage).toHaveBeenCalled());

        // Exportar PDF y CSV
        fireEvent.click(screen.getByText('Exportar a PDF'));
        fireEvent.click(screen.getByText('Exportar a CSV'));
        expect(handleExportPDF).toHaveBeenCalled();
        expect(handleExportCSV).toHaveBeenCalled();
    });
});
