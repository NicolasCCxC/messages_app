import { render, fireEvent } from '@testing-library/react';
import { useOutsideClick } from '@hooks/useOutsideClick';

function Demo({ onOutside }: { onOutside: () => void }) {
  const ref = useOutsideClick(onOutside);
  return (
    <div data-testid="root">
      <div data-testid="inside" ref={ref}>
        inside
      </div>
      <div data-testid="sibling">sibling</div>
    </div>
  );
}

describe('useOutsideClick', () => {
  it('no dispara cuando clickeo dentro', () => {
    const cb = jest.fn();
    const { getByTestId } = render(<Demo onOutside={cb} />);

    fireEvent.mouseDown(getByTestId('inside'));
    expect(cb).not.toHaveBeenCalled();
  });

  it('dispara cuando clickeo fuera', () => {
    const cb = jest.fn();
    const { getByTestId } = render(<Demo onOutside={cb} />);

    fireEvent.mouseDown(getByTestId('sibling'));
    expect(cb).toHaveBeenCalledTimes(1);
  });

  it('deja de escuchar al hacer unmount', () => {
    const cb = jest.fn();
    const { unmount } = render(<Demo onOutside={cb} />);

    unmount();
    fireEvent.mouseDown(document.body);
    expect(cb).not.toHaveBeenCalled();
  });
});
