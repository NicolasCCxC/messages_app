import { IconName } from '@components/icon';
import { IconColor } from '@constants/Icon';

export const getIconName = (name: string): string => {
    let result = '';
    for (const letter of name) {
        result += letter === letter.toUpperCase() ? `-${letter.toLowerCase()}` : letter;
    }
    return result;
};

export const getIconVariant = (icon: IconName, variant: IconColor): IconName => {
    return icon.replace(IconColor.Default, variant) as IconName;
};
