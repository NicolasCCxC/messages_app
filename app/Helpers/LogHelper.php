<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Infrastructure\Services\ErrorClassifier;
use Illuminate\Support\Str;

class LogHelper
{
    protected $errorClassifier;

    public function __construct(ErrorClassifier $errorClassifier)
    {
        $this->errorClassifier = $errorClassifier;
    }

    /**
     * Generates a unique error number with a given prefix.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateErrorNumber(string $prefix): string
    {
        $formattedErrorType = strtoupper(Str::slug($prefix, '-'));
        $uniqueNumber = strtoupper(Str::random(6));
        return "$formattedErrorType-$uniqueNumber";
    }

    /**
     * Saves a log entry to the database and, as fallback, to a text file.
     *
     * @param mixed $responseData
     * @param int $statusCode
     * @param string $message
     * @param string|null $errorCode
     * @return void
     */
    public static function saveLog($responseData, int $statusCode, string $message = '', ?string $errorCode = null, ?string $companyId = null, ?string $userId = null): void
    {
        $method = Request::method();
        $url = Request::fullUrl();
        $requestData = json_encode(Request::all());
        $companyId = Request::header('company-id', $companyId);
        $userId = Request::header('user-id', $userId);

        $errorClassifier = new ErrorClassifier();
        $errorType = $errorClassifier->classifyError($statusCode, $responseData);

        if (empty($errorCode) && $statusCode >= 400) {
            $errorCode = self::generateErrorNumber($errorType);
        }

        $responseJson = is_string($responseData)
            ? json_encode(['message' => $responseData])
            : json_encode($responseData);

        $logData = [
            'company_id'  => $companyId,
            'user_id'     => $userId,
            'method'      => $method,
            'path'        => $url,
            'message'     => $message,
            'response'    => $responseJson,
            'request'     => $requestData,
            'error_code'  => $errorCode,
            'error_type'  => $errorType,
            'status_code' => $statusCode,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

        try {
            DB::connection('pgsql_logs')->table('logs')->insert($logData);
        } catch (\Exception $e) {
            self::saveLogToFile($logData, $e->getMessage());
        }
    }

    /**
     * Saves a log entry to a text file as a fallback.
     *
     * @param array $logData
     * @param string $errorMessage // Mensaje del error que causó el fallo en la base de datos
     * @return void
     */
    private static function saveLogToFile(array $logData, string $errorMessage): void
    {
        $logData['db_error'] = $errorMessage;
        $logData['timestamp'] = now()->toDateTimeString();
        $logText = json_encode($logData, JSON_PRETTY_PRINT);
        $fileName = 'logs/' . now()->format('Y-m-d') . '.log';
        Storage::append($fileName, $logText);
    }
}
