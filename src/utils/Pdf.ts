import { CM_PER_INCH, DPI } from '@constants/Pdf';
import type { IMargins } from '@pages/pdf-presentation/context';

export const cmToPx = (cm: number): number => (cm / CM_PER_INCH) * DPI;

export const getCssMarginVars = ({ top, right, bottom, left }: IMargins): React.CSSProperties =>
    ({
        '--mt': `${top}px`,
        '--mr': `${right}px`,
        '--mb': `${bottom}px`,
        '--ml': `${left}px`,
    } as React.CSSProperties);

export const convertMarginsToPx = ({ top, right, bottom, left }: IMargins): IMargins => ({
    top: cmToPx(top),
    right: cmToPx(right),
    bottom: cmToPx(bottom),
    left: cmToPx(left),
});
