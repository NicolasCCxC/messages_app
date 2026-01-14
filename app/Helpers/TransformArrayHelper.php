<?php

namespace App\Helpers;

/**
 * Class TransformArrayHelper transform structure of arrays
 */
class TransformArrayHelper
{

    /**
     * Specific function to transform array like $data[configuration] to $data => ['configuration']
     *
     * @param array $data To transform
     *
     * @return array The result
     */
    public static function transformBracketNotationToMultidimensional(array $data): array
    {
        $result = [];

        foreach ($data as $properties => $value) {

            // Extract keys
            $keys = array_reverse(explode('[', $properties));

            // Save each property
            foreach ($keys as $key) {
                $value = [
                    str_replace(']', '', $key) => $value
                ];
            }

            // Merge result
            $result = array_merge_recursive($result, $value);
        }

        return $result;
    }

}
