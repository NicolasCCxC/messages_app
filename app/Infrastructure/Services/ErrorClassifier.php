<?php

namespace App\Infrastructure\Services;

class ErrorClassifier
{
    /**
     * Classifies the type of error based on the status code.
     *
     * @param int $statusCode
     * @param string $responseData
     * @return string
     */
    public function classifyError(int $statusCode, string $responseData)
    {
        if ($statusCode >= 500)  return 'Server Error';

        if ($statusCode >= 400 && $statusCode < 500) return 'Client Error';

        return '';
    }
}
