type Comparator<T> = (originalValue: T[keyof T], modifiedValue: T[keyof T]) => boolean;

type DiffOptions<T> = {
    ignoreKeys?: (keyof T)[];
    customComparators?: Partial<{ [K in keyof T]: Comparator<T> }>;
};

/**
 * Compares two objects and returns the properties that have changed between them.
 * Allows specifying keys to ignore and custom comparison functions for specific keys.
 *
 * @template T - Generic type extending Record<string, unknown>
 * @param {T | null | undefined} original - Original object to compare against
 * @param {T | null | undefined} modified - Modified object to check for differences
 * @param {DiffOptions<T>} [options] - Optional configuration for the comparison (keys to ignore and custom comparators)
 * @returns {Partial<T>} - Object containing only the properties that differ between `original` and `modified`
 */
export const getDiff = <T extends Record<string, unknown>>(
    original: T | null | undefined,
    modified: T | null | undefined,
    options?: DiffOptions<T>
): Partial<T> => {
    if (!original || !modified) return {};

    const diff: Partial<T> = {};
    const keys = Object.keys(modified) as (keyof T)[];

    keys.forEach(key => {
        if (options?.ignoreKeys?.includes(key)) return;

        const originalValue = original[key];
        const modifiedValue = modified[key];
        const customCompare = options?.customComparators?.[key];

        const isEqual = customCompare ? customCompare(originalValue, modifiedValue) : originalValue === modifiedValue;

        if (!isEqual) {
            diff[key] = modifiedValue;
        }
    });

    return diff;
};

export type { Comparator, DiffOptions };
