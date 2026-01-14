<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class UtilsHelper
{

    /**
     * @throws GuzzleException
     */
    public static function dynamicResource(array $data)
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/utils/custom-query/',
                'method' => 'POST',
                'service' => 'UTILS',
                'data' => $data,
                'user_id' => Str::uuid()->toString(),
                'company_id' => Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception) {}
    }

    /**
     * @throws GuzzleException
     */
    public static function getUtils(array $data)
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/utils/dynamic-request',
                'method' => 'POST',
                'service' => 'UTILS',
                'data' => $data,
                'user_id' => Str::uuid()->toString(),
                'company_id' => Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception) {
            \Log::error('dynamic request error: ' . $exception->getMessage(), [
                'exception' => $exception
            ]);
        }
    }
}