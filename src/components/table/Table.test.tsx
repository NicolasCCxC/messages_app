import { render, screen } from '@testing-library/react';
import { Table } from './Table';
import { PaginationBoundaries } from '@constants/Paginator';
import { ITableProps } from '@models/Table';
import { IGenericRecord } from '@models/GenericRecord';

jest.mock('./components/Header', () => ({
    Header: jest.fn(() => <thead data-testid="header" />),
}));
jest.mock('./components/Body', () => ({
    Body: jest.fn(() => <tbody data-testid="body" />),
}));
jest.mock('./components/Paginator', () => ({
    Paginator: jest.fn(() => <div data-testid="paginator">Paginator</div>),
}));

describe('Table Component', () => {
    const baseProps: ITableProps = {
        customIcons: (item: IGenericRecord) => <p>{item.icon}</p>,
        fields: {
            header: [{ value: 'Nombre' }],
            body: [{ name: 'nombre' }],
            required: ['nombre'],
        },
        editing: {
            onFieldChange: jest.fn(),
        },
        data: {
            all: [],
            current: [],
            pages: 1,
            update: jest.fn(),
        },
        search: {
            showMessage: false,
            value: '',
        },
    };

    it('debe renderizar Header y Body con props correctos', () => {
        render(<Table {...baseProps} />);
        expect(screen.getByTestId('header')).toBeInTheDocument();
        expect(screen.getByTestId('body')).toBeInTheDocument();
    });

    it('debe mostrar mensaje cuando no hay resultados y search.showMessage=true', () => {
        render(
            <Table {...baseProps} data={{ ...baseProps.data, current: [] }} search={{ ...baseProps.search, showMessage: true }} />
        );
        expect(screen.getByText('*No se han encontrado resultados para esta búsqueda')).toBeInTheDocument();
    });

    it('no debe mostrar mensaje si hay resultados', () => {
        render(
            <Table
                {...baseProps}
                data={{ ...baseProps.data, current: [{ nombre: 'Juan' }] }}
                search={{ ...baseProps.search, showMessage: true }}
            />
        );
        expect(screen.queryByText('*No se han encontrado resultados para esta búsqueda')).not.toBeInTheDocument();
    });

    it('debe renderizar Paginator si data.pages > MinPage', () => {
        render(<Table {...baseProps} data={{ ...baseProps.data, pages: PaginationBoundaries.MinPage + 1 }} />);
        expect(screen.getByTestId('paginator')).toBeInTheDocument();
    });

    it('no debe renderizar Paginator si data.pages <= MinPage', () => {
        render(<Table {...baseProps} />);
        expect(screen.queryByTestId('paginator')).not.toBeInTheDocument();
    });

    it('debe aplicar wrapperClassName correctamente', () => {
        render(<Table {...baseProps} wrapperClassName="custom-wrapper" />);
        const wrapper = screen.getByRole('table').parentElement;
        expect(wrapper).toHaveClass('w-max custom-wrapper');
    });
});
