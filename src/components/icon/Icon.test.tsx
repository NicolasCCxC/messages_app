import { render, screen, fireEvent } from '@testing-library/react';
import { Icon } from './Icon';

jest.mock('@utils/Icon', () => ({
  getIconName: (n: string) => (n === 'hover' ? 'hover' : 'main'),
}));

jest.mock('@components/form', () => ({ ENTER: 'Enter' }));

jest.mock('../../assets/icons/main.svg', () => ({ __esModule: true, default: 'main-src' }), { virtual: true });
jest.mock('../../assets/icons/hover.svg', () => ({ __esModule: true, default: 'hover-src' }), { virtual: true });

describe('Icon', () => {
  it('muestra placeholder mientras carga y luego <img> con src', async () => {
    render(<Icon name="plusWhite" className="x" />);

    expect(document.querySelector('.icon-placeholder')).toBeTruthy();

    const img = await screen.findByRole('button');
    expect(img).toHaveAttribute('src', 'main-src');
    expect(img).toHaveAttribute('alt', 'plusWhite');
  });

    it('no cambia el src si hoverIcon === name (maneja over/leave sin swap)', async () => {
        render(<Icon name="cancelWhite" hoverIcon="cancelWhite" />);
    
        const img = await screen.findByRole('button');
        const initial = img.getAttribute('src');
        expect(initial).toBeTruthy();

        fireEvent.mouseOver(img);
        expect(img).toHaveAttribute('src', initial);
    
        fireEvent.mouseLeave(img);
        expect(img).toHaveAttribute('src', initial);
    });  

  it('ejecuta onKeyDown al presionar Enter', async () => {
    const onKeyDown = jest.fn();
    render(<Icon name="arrowDown" onKeyDown={onKeyDown} />);

    const img = await screen.findByRole('button');
    fireEvent.keyDown(img, { key: 'Enter' });

    expect(onKeyDown).toHaveBeenCalled();
  });
});
