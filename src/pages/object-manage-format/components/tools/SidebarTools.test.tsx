import { render, screen } from '@testing-library/react';
import { SidebarTools } from './SidebarTools';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { ElementType } from '@constants/ObjectsEditor';
import type { ContextType } from 'react';

jest.mock('./index', () => {
  const actual = jest.requireActual('@constants/ObjectsEditor');
  const { ElementType } = actual;

  const TextToolMock = () => <div data-testid="text-tool">Text Tool</div>;
  const ShapeToolMock = () => <div data-testid="shape-tool">Shape Tool</div>;
  const ImageToolMock = () => <div data-testid="image-tool">Image Tool</div>;
  const TableToolMock = () => <div data-testid="table-tool">Table Tool</div>;

  return {
    ELEMENT_TOOLS: {
      [ElementType.Text]: TextToolMock,
      [ElementType.Shape]: ShapeToolMock,
      [ElementType.Image]: ImageToolMock,
      [ElementType.Table]: TableToolMock,
    },
  };
});
describe('SidebarTools Component', () => {
    const mockContextValue: ContextType<typeof ManageObjectContext> = {
        element: {
            productId: 'p1',
            name: 'Test Element',
            identifier: 'test1',
            objectType: 'GENERIC' as any,
            type: 'TEST',
        },
        updateElementStyles: jest.fn(),
        updateElementProperties: jest.fn(),
        setElement: jest.fn(),
        handleClickElement: jest.fn(),
        selectedElementType: null,
        setSelectedElementType: jest.fn(),
    };

    const renderSidebarTools = (selectedElementType: ElementType | null = null) => {
        return render(
            <ManageObjectContext.Provider value={{ ...mockContextValue, selectedElementType }}>
                <SidebarTools />
            </ManageObjectContext.Provider>
        );
    };

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería renderizar correctamente sin mostrar herramientas cuando no hay elemento seleccionado', () => {
        renderSidebarTools();

        const sidebarContainer = screen.getByTestId('sidebar-container');
        expect(sidebarContainer).toHaveClass('w-[13.25rem]');
        expect(sidebarContainer).toHaveClass('bg-gray-light');

        expect(screen.queryByText('Herramientas')).not.toBeInTheDocument();
        expect(screen.queryByTestId('text-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('shape-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('image-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('table-tool')).not.toBeInTheDocument();
    });

    it('debería mostrar la herramienta de texto cuando el tipo de elemento seleccionado es Text', () => {
        renderSidebarTools(ElementType.Text);

        expect(screen.getByText('Herramientas')).toBeInTheDocument();

        expect(screen.getByTestId('text-tool')).toBeInTheDocument();

        expect(screen.queryByTestId('shape-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('image-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('table-tool')).not.toBeInTheDocument();
    });

    it('debería mostrar la herramienta de forma cuando el tipo de elemento seleccionado es Shape', () => {
        renderSidebarTools(ElementType.Shape);

        expect(screen.getByText('Herramientas')).toBeInTheDocument();

        expect(screen.getByTestId('shape-tool')).toBeInTheDocument();

        expect(screen.queryByTestId('text-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('image-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('table-tool')).not.toBeInTheDocument();
    });

    it('debería mostrar la herramienta de imagen cuando el tipo de elemento seleccionado es Image', () => {
        renderSidebarTools(ElementType.Image);

        expect(screen.getByText('Herramientas')).toBeInTheDocument();

        expect(screen.getByTestId('image-tool')).toBeInTheDocument();

        expect(screen.queryByTestId('text-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('shape-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('table-tool')).not.toBeInTheDocument();
    });

    it('debería mostrar la herramienta de tabla cuando el tipo de elemento seleccionado es Table', () => {
        renderSidebarTools(ElementType.Table);

        expect(screen.getByText('Herramientas')).toBeInTheDocument();

        expect(screen.getByTestId('table-tool')).toBeInTheDocument();

        expect(screen.queryByTestId('text-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('shape-tool')).not.toBeInTheDocument();
        expect(screen.queryByTestId('image-tool')).not.toBeInTheDocument();
    });
});
