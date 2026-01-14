<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ElectronicInvoiceHelper
{

    /**
     * @throws GuzzleException
     */
    public static function getResolutions(array $data, string $companyId = '')
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/api/electronic-invoice/resolutions',
                'method' => 'POST',
                'service' => 'ELECTRONIC_INVOICE',
                'data' => $data,
                'user_id' => Str::uuid()->toString(),
                'company_id' => $companyId ?? Str::uuid()->toString(),
            ])['data'];
        }catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Log::info($exception);
        }
    }
}
