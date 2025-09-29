import { render, screen } from '@testing-library/react';
import About from '@pages/About';

describe('About page', () => {
  it('renderiza título y descripción', () => {
    render(<About />);
    expect(screen.getByRole('heading', { name: /about us/i })).toBeInTheDocument();
    expect(screen.getByText(/learn more about our company/i)).toBeInTheDocument();
  });
});
