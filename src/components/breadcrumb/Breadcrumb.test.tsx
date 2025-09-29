import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { Breadcrumb } from './Breadcrumb';

describe('Breadcrumb', () => {
  it('muestra los items con sus enlaces y separadores', () => {
    const items = [
      { title: 'Inicio', path: '/' },
      { title: 'Sección', path: '/sec' },
      { title: 'Página', path: '/sec/pag' },
    ];

    render(
      <MemoryRouter>
        <Breadcrumb items={items} />
      </MemoryRouter>
    );

    items.forEach(({ title }) => {
      expect(screen.getByRole('link', { name: title })).toBeInTheDocument();
    });

    const seps = screen.getAllByText('>', { exact: false });
    expect(seps.length).toBe(items.length - 1);

    expect(screen.getByRole('link', { name: 'Inicio' })).toHaveAttribute('href', '/');
    expect(screen.getByRole('link', { name: 'Sección' })).toHaveAttribute('href', '/sec');
    expect(screen.getByRole('link', { name: 'Página' })).toHaveAttribute('href', '/sec/pag');
  });

  it('acepta className adicional', () => {
    const items = [{ title: 'Solo', path: '/' }];
    render(
      <MemoryRouter>
        <Breadcrumb items={items} className="extra-bread" />
      </MemoryRouter>
    );
    expect(screen.getByLabelText('Breadcrumb')).toHaveClass('extra-bread');
  });
});
