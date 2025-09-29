import { render, screen } from '@testing-library/react';
import * as RR from 'react-router-dom';
import NotFound from '@pages/NotFound';

jest.mock('react-router-dom', () => {
  const actual = jest.requireActual('react-router-dom');
  return {
    ...actual,
    useRouteError: jest.fn(),
    isRouteErrorResponse: jest.fn(),
  };
});

describe('NotFound page', () => {
  const useRouteErrorMock = RR.useRouteError as unknown as jest.Mock;
  const isRouteErrorResponseMock = RR.isRouteErrorResponse as unknown as jest.Mock;

  beforeEach(() => {
    useRouteErrorMock.mockReset();
    isRouteErrorResponseMock.mockReset();
  });

  it('muestra detalle cuando es RouteErrorResponse', () => {
    useRouteErrorMock.mockReturnValue({ statusText: 'Not Found' });
    isRouteErrorResponseMock.mockReturnValue(true);

    render(<NotFound />);

    expect(screen.getByText(/oops!/i)).toBeInTheDocument();
    expect(screen.getByText(/unexpected error has occurred/i)).toBeInTheDocument();
    expect(screen.getByText(/not found/i)).toBeInTheDocument();
  });

  it('muestra fallback "Oops" cuando NO es RouteErrorResponse', () => {
    useRouteErrorMock.mockReturnValue(undefined);
    isRouteErrorResponseMock.mockReturnValue(false);

    render(<NotFound />);

    expect(screen.getByText('Oops')).toBeInTheDocument();
  });
});
