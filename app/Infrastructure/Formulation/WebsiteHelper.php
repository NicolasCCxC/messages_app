<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WebsiteHelper
{
    /**
     * @throws GuzzleException
     */
    public static function getWebsite(array $data)
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/websites',
                'method' => 'POST',
                'service' => 'WEBSITE',
                'data' => $data,
                'user_id' => auth()->guard('client-api')->user()->id ?? Str::uuid()->toString(),
                'company_id' => Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception)
        {
            throw new BadRequestHttpException('Website does not exist');
        }
    }

    /**
     * @throws
     */
    public static function updateLogo(array $data)
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/websites/bucket/'.$data['company_id'].'/'.$data['bucket_id'],
                'method' => 'PUT',
                'service' => 'WEBSITE',
                'data' => $data,
                'user_id' => auth()->guard('client-api')->user()->id ?? Str::uuid()->toString(),
                'company_id' => Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception)
        {
            Log::info("Error on GatewayHelper-handler: " . $exception->getMessage());
        }
    }

    public static function updateDomain(array $data)
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/websites/update-domain',
                'method' => 'PUT',
                'service' => 'WEBSITE',
                'data' => $data,
                'user_id' => auth()->guard('client-api')->user()->id ?? Str::uuid()->toString(),
                'company_id' => Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception)
        {
            Log::info("Error on GatewayHelper-handler: " . $exception->getMessage());
        }
    }

    public static function getDomain(string $companyId)
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/websites/domain/'.$companyId,
                'method' => 'GET',
                'service' => 'WEBSITE',
                'data' => [],
                'user_id' => auth()->guard('client-api')->user()->id ?? Str::uuid()->toString(),
                'company_id' => Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception)
        {
            Log::info("Error on GatewayHelper-handler: " . $exception->getMessage());
        }
    }
}
