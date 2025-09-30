import { fireEvent, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { ShapeTool } from './ShapeTool';
import { ManageObjectContext, IElement } from '@pages/object-manage-format/context';
import type { ContextType } from 'react';
import { ObjectType } from '@constants/ObjectsEditor';
import { SHAPE_STYLE_TOOLS } from '.';

jest.mock('./ColorPicker', () =>
    jest.fn(({ onChange, value }) => (
        <input
            data-testid="mock-color-picker"
            type="text"
            value={value || ''}
            onChange={e => onChange({ target: { value: e.target.value } })}
        />
    ))
);

jest.mock('./SizeControls', () => ({
    SizeControls: jest.fn(() => <div data-testid="mock-size-controls">SizeControls</div>),
}));

jest.mock('@components/icon', () => ({
    Icon: jest.fn(({ name }) => <div data-testid={`icon-${name}`}>{name}</div>),
}));

const mockUpdateElementStyles = jest.fn();

const baseElement: IElement = {
    productId: 'p1',
    name: 'Shape Element',
    identifier: 'shape1',
    objectType: ObjectType.Generic,
    type: 'SHAPE',
    style: {
        backgroundColor: '#FFFFFF',
        borderColor: '#000000',
        borderTopLeftRadius: '0',
        borderTopRightRadius: '0',
        borderBottomLeftRadius: '0',
        borderBottomRightRadius: '0',
    },
};

describe('ShapeTool Component', () => {
    const mockContextValue: ContextType<typeof ManageObjectContext> = {
        element: baseElement,
        updateElementStyles: mockUpdateElementStyles,
        updateElementProperties: jest.fn(),
        setElement: jest.fn(),
        handleClickElement: jest.fn(),
        selectedElementType: null,
        setSelectedElementType: jest.fn(),
    };

    const renderShapeTool = (element: IElement = baseElement) => {
        return render(
            <ManageObjectContext.Provider value={{ ...mockContextValue, element }}>
                <ShapeTool />
            </ManageObjectContext.Provider>
        );
    };

    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('debería renderizar correctamente con todos los controles', () => {
        renderShapeTool();

        expect(screen.getByText('Fondo')).toBeInTheDocument();
        expect(screen.getByText('Color')).toBeInTheDocument();

        expect(screen.getByText('Color de borde')).toBeInTheDocument();

        expect(screen.getByText('Borde redondo')).toBeInTheDocument();

        SHAPE_STYLE_TOOLS.forEach(({ iconName }) => {
            expect(screen.getByTestId(`icon-${iconName}`)).toBeInTheDocument();
        });

        expect(screen.getByTestId('mock-size-controls')).toBeInTheDocument();
    });

    it('debería llamar a updateElementStyles al cambiar el color de fondo', async () => {
        const user = userEvent.setup();
        renderShapeTool();

        const colorPickers = screen.getAllByTestId('mock-color-picker');
        const backgroundColorPicker = colorPickers[0];

        await user.clear(backgroundColorPicker);
        await user.type(backgroundColorPicker, '#FF0000');

        fireEvent.change(backgroundColorPicker, {
            target: { value: '#FF0000' },
        });

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('backgroundColor', '#FF0000');
    });

    it('debería llamar a updateElementStyles al cambiar el color de borde', async () => {
        const user = userEvent.setup();
        renderShapeTool();

        const colorPickers = screen.getAllByTestId('mock-color-picker');
        const borderColorPicker = colorPickers[1];

        await user.clear(borderColorPicker);
        await user.type(borderColorPicker, '#00FF00');

        fireEvent.change(borderColorPicker, {
            target: { value: '#00FF00' },
        });

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('borderColor', '#00FF00');
    });

    it('debería llamar a updateElementStyles al cambiar el radio de borde superior izquierdo', async () => {
        const user = userEvent.setup();
        renderShapeTool();

        const borderRadiusInputs = screen.getAllByPlaceholderText('0');

        await user.clear(borderRadiusInputs[0]);
        await user.type(borderRadiusInputs[0], '10');

        fireEvent.change(borderRadiusInputs[0], {
            target: { value: '10' },
        });

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('borderTopLeftRadius', '10');
    });

    it('debería llamar a updateElementStyles al cambiar todos los radios de borde', async () => {
        const user = userEvent.setup();
        renderShapeTool();

        const borderRadiusInputs = screen.getAllByPlaceholderText('0');

        await user.clear(borderRadiusInputs[1]);
        await user.type(borderRadiusInputs[1], '20');
        fireEvent.change(borderRadiusInputs[1], {
            target: { value: '20' },
        });

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('borderTopRightRadius', '20');

        await user.clear(borderRadiusInputs[2]);
        await user.type(borderRadiusInputs[2], '30');
        fireEvent.change(borderRadiusInputs[2], {
            target: { value: '30' },
        });

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('borderBottomLeftRadius', '30');

        await user.clear(borderRadiusInputs[3]);
        await user.type(borderRadiusInputs[3], '40');
        fireEvent.change(borderRadiusInputs[3], {
            target: { value: '40' },
        });

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('borderBottomRightRadius', '40');
    });

    it('debería manejar correctamente los valores vacíos en los inputs de radio de borde', async () => {
        const user = userEvent.setup();
        renderShapeTool();

        const borderRadiusInputs = screen.getAllByPlaceholderText('0');

        await user.clear(borderRadiusInputs[0]);

        expect(mockUpdateElementStyles).toHaveBeenCalledWith('borderTopLeftRadius', '');
    });

    it('debería mostrar los valores actuales del elemento en los inputs', () => {
        const elementWithStyles: IElement = {
            ...baseElement,
            style: {
                ...baseElement.style,
                backgroundColor: '#FF0000',
                borderColor: '#00FF00',
                borderTopLeftRadius: '10',
                borderTopRightRadius: '20',
                borderBottomLeftRadius: '30',
                borderBottomRightRadius: '40',
            },
        };

        renderShapeTool(elementWithStyles);

        const colorPickers = screen.getAllByTestId('mock-color-picker');
        expect(colorPickers[0]).toHaveValue('#FF0000');
        expect(colorPickers[1]).toHaveValue('#00FF00');

        const borderRadiusInputs = screen.getAllByPlaceholderText('0');
        expect(borderRadiusInputs[0]).toHaveValue(10);
        expect(borderRadiusInputs[1]).toHaveValue(20);
        expect(borderRadiusInputs[2]).toHaveValue(30);
        expect(borderRadiusInputs[3]).toHaveValue(40);
    });
});
