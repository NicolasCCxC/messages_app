/* eslint-disable @typescript-eslint/no-explicit-any */
import { render, screen, fireEvent, within } from '@testing-library/react';
import { Paginator } from './Paginator';
import { TableContext } from '../context';

jest.mock('@components/icon', () => ({
  Icon: ({ name, onClick, onKeyDown }: any) => (
    <button data-testid={name} onClick={onClick} onKeyDown={onKeyDown}>
      I
    </button>
  ),
}));

const onPageChange = jest.fn();

const renderPaginator = (pages = 10) => {
  return render(
    <TableContext.Provider value={{ data: { pages }, editing: { onPageChange } } as any}>
      <Paginator searchValue="q" />
    </TableContext.Provider>
  );
};

describe('Paginator (rápido y sincrónico)', () => {
  beforeEach(() => onPageChange.mockClear());

  it('navega con flechas y crea el rango con puntos/first/last', () => {
    const { container } = renderPaginator(10);
    const root = container.querySelector('.paginator') as HTMLElement;

    const hdr1 = within(root).getAllByText((_, node) => node?.textContent?.includes('Página 1 de 10') ?? false);
    expect(hdr1[0]).toBeInTheDocument();

    fireEvent.click(screen.getByTestId('arrowRightBlueOutline'));
    expect(onPageChange).toHaveBeenCalledWith(1, 'q');

    const hdr2 = within(root).getAllByText((_, node) => node?.textContent?.includes('Página 2 de 10') ?? false);
    expect(hdr2[0]).toBeInTheDocument();

    expect(within(root).getAllByText('...').length).toBeGreaterThan(0);
    const lastBtn = within(root).getByRole('button', { name: '10' });
    expect(lastBtn).toBeInTheDocument();

    fireEvent.click(lastBtn);
    expect(onPageChange).toHaveBeenCalledWith(9, 'q');
    const hdrLast = within(root).getAllByText((_, node) => node?.textContent?.includes('Página 10 de 10') ?? false);
    expect(hdrLast[0]).toBeInTheDocument();
    
    fireEvent.click(screen.getByTestId('arrowLeftBlueOutline'));
    expect(onPageChange).toHaveBeenCalledWith(8, 'q');
    const hdr9 = within(root).getAllByText((_, node) => node?.textContent?.includes('Página 9 de 10') ?? false);
    expect(hdr9[0]).toBeInTheDocument();
  });
});
