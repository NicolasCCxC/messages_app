import { IGenericRecord } from '@models/GenericRecord';

export const getRequiredFields = (campos: IGenericRecord[]): string => {
    return campos
        .map(campo => {
            const valor = campo.isFixed ? campo.content : campo.inputStructureProduct?.fieldName;

            if (!valor) return '';

            const color = campo.isFixed ? '#A9A9AC' : '#4B4B4B'; // gris claro o negro

            return `<span style="color: ${color}">${valor}</span>`;
        })
        .filter(Boolean)
        .join(' ; ');
};
