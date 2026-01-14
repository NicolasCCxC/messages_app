<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class ShoppingHelper
{
    /**
     * @throws GuzzleException
     */
    public static function getShopCart($companyId)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/shopping/shop-carts',
            'method' => 'GET',
            'service' => 'SHOPPING',
            'user_id' => auth()->guard('client-api')->user()->id ?? Str::uuid()->toString(),
            'company_id' => $companyId,
            'data' => []
        ])['data'];
    }
}
