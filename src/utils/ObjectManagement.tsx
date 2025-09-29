import { CSSProperties } from 'react';

export const getBorders = (style?: CSSProperties): Record<string, number> =>
    Object.fromEntries(
        ['borderTopLeftRadius', 'borderTopRightRadius', 'borderBottomLeftRadius', 'borderBottomRightRadius'].map(prop => [
            prop,
            parseInt(style?.[prop as keyof CSSProperties] as string) || 0,
        ])
    );
