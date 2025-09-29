import { IGenericRecord } from '@models/GenericRecord';
import { IFields } from '@models/Table';

export const handleExportCSV = (TABLE_FIELDS: IFields, data: IGenericRecord[]): void => {
    const headerTitles = TABLE_FIELDS.header.map(h => h.value);
    const fieldKeys = TABLE_FIELDS.body.map(b => b.name);

    const csvRows = [headerTitles.join(','), ...data.map(row => fieldKeys.map(key => row[key]).join(','))];
    const csvContent = csvRows.join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'documento.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

export const handleExportPDF = (): void => {
    const table = document.querySelector('table');
    if (!table) return;

    const newWindow = window.open('', '', 'width=800,height=600');
    if (!newWindow) return;

    newWindow.document.write(`
        <html>
            <head>
                <title>Tabla</title>
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        background-color: red !important;
                    }
                    th, td {
                        border: 1px solid #ccc;
                        padding: 8px;
                        text-align: left;
                        background-color: white !important;
                    }
                    input:disabled {
                        background-color: #fff !important;
                        border: none !important;
                    }
                </style>
            </head>
            <body>
                ${table.outerHTML}
            </body>
        </html>
    `);

    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
};
