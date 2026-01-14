<?php

namespace App\Traits;

use App\Enums\Services;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Example trait
 */
trait ResponseApiTrait
{
    /**
     * Success response
     *
     * @param mixed $data Data from response
     * @param string $service Consumed service
     * @param int $statusCode Code http for response
     * @param string $message Info message
     * @return JsonResponse Object with response
     */
    public function successResponse($data, int $statusCode = Response::HTTP_ACCEPTED, string $message = 'Success operation'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'service' => Services::PAY,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Error response
     *
     * @param string $service
     * @param int $statusCode
     * @param string $message
     * @return JsonResponse
     */
    public function errorResponse(int $statusCode, string $message = 'An error has occurred', $errors = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'service' => Services::PAY,
            'errors' => $errors,
        ], $statusCode);
    }

}
