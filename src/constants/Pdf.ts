export enum TabName {
    Fields = 'FIELDS',
    Objects = 'OBJECTS',
    Format = 'FORMAT',
}

export enum PaperSize {
    Letter = 'LETTER',
    Legal = 'LEGAL',
    A4 = 'A4',
}

export const PAPER_SIZES = [
    {
        label: 'Carta',
        value: PaperSize.Letter,
    },
    {
        label: 'Oficio',
        value: PaperSize.Legal,
    },
    {
        label: 'A4',
        value: PaperSize.A4,
    },
];

export const PAPER_DIMENSIONS: { [key in PaperSize]: { width: number; height: number; minHeight: number } } = {
    [PaperSize.Letter]: {
        width: 816,
        height: 1056,
        minHeight: 1056,
    },
    [PaperSize.Legal]: {
        width: 816,
        height: 1344,
        minHeight: 1344,
    },
    [PaperSize.A4]: {
        width: 794,
        height: 1123,
        minHeight: 1123,
    },
};

export const FONTS = [
    {
        label: 'Arial',
        value: 'arial',
    },
    {
        label: 'Times new roman',
        value: 'times',
    },
];

export const DPI = 96;
export const CM_PER_INCH = 2.54;
export const FIELD = 'FIELD';
