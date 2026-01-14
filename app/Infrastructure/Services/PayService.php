<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Formulation\GatewayHelper;
use App\Traits\CommunicationBetweenServicesTrait;
use Illuminate\Support\Str;

class PayService
{


    use CommunicationBetweenServicesTrait;

    public function payAndCreateToken (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/recurring-payment-registration',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);
    }

    public function deleteCreditCardToken (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/delete-card-token',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);
    }

    public function payWithToken (array $data, string $company_id = null)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/payment-with-token',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? $company_id ?? Str::uuid()->toString(),
        ]);
    }
    public function payWithoutToken (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/payment-without-token',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);

    }
    public function payPse (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/pse',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);
    }

    public static function getDetailTransaction (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/get-details-transaction',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ])['data'] ?? [];

    }

    public function createToken (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/pays/membership/get-card-token',
            'method' => 'POST',
            'service' => 'PAYS',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);
    }

    public function getDataCompanyPayu(string $company_id)
    {
        return GatewayHelper::routeHandler([
            'resource' => "/pays/membership/get-payu-data/{$company_id}",
            'method' => 'GET',
            'service' => 'PAYS',
            'data' => [],
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);
    }
}
