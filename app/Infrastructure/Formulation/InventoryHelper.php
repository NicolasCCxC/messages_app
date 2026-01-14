<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class InventoryHelper
{
    /**
     * @throws GuzzleException
     */
    public static function updateCategoriesAndProductTypesDefault(array $data, string $companyId = '')
    {
        try {
            return GatewayHelper::routeHandler([
                'resource' => '/inventories/categories/default/',
                'method' => 'POST',
                'service' => 'INVENTORY',
                'data' => $data,
                'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
                'company_id' => auth()->user()->company_id ?? $companyId,
            ])['data'];
        }catch (\Exception $exception) {
        }
    }
}
