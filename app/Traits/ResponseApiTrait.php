<?php

namespace App\Traits;

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
    public function successResponse($data, string $service, string $message = 'Success operation', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'service' => $service,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Error response
     *
     * @param string $service
     * @param int $statusCode
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function errorResponse(string $service, int $statusCode, string $message = 'An error has occurred', array $errors = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'service' => $service,
            'errors' => $errors,
        ], $statusCode);
    }

}
