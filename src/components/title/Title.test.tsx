import { render, screen } from '@testing-library/react';
import { Title } from './Title';

describe('Title', () => {
  it('renderiza el título recibido por props', () => {
    render(<Title title="Mi Título" />);
    expect(screen.getByRole('heading', { name: 'Mi Título' })).toBeInTheDocument();
  });

  it('agrega className adicional', () => {
    render(<Title title="Otro" className="extra-class" />);
    const h1 = screen.getByRole('heading', { name: 'Otro' });
    expect(h1).toHaveClass('extra-class');
  });
});
