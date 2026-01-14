<?php

namespace App\Http\Middleware;

use App\Helpers\LogHelper;
use Closure;

class SetDatabaseConnection
{

    const HTTP_ERROR_THRESHOLD = 400;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $logLevel = env('LOG_LEVEL', 'info');

        $logActions = [
            'info' => function () use ($response) {
                $this->logResponse($response);
            },
            'error' => function () use ($response) {
                if ($response->getStatusCode() >= self::HTTP_ERROR_THRESHOLD) $this->logResponse($response);
            },
            'success' => function () use ($response) {
                if ($response->getStatusCode() < self::HTTP_ERROR_THRESHOLD) $this->logResponse($response);
            }
        ];

        if (isset($logActions[$logLevel])) $logActions[$logLevel]();

        return $response;
    }

    /**
     * Private method to register the response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    private function logResponse($response)
    {
        LogHelper::saveLog(
            json_encode($response->getContent()),
            $response->getStatusCode()
        );
    }
}
