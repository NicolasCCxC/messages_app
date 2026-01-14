<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Formulation\GatewayHelper;
use Illuminate\Support\Str;
use App\Models\Company;

class InventoryService
{

    /**
     * Get membership billing services
     *
     * @param array $uniqueProductsIds is an array of UUIDs of each unique product purchased
     * @return array Services
     * @throws GuzzleException
     */
    public function getServicesByUniqueProductId(array $uniqueProductsIds)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/inventories/unique-products/by-unique-product-ids',
            'method' => 'POST',
            'service' => 'INVENTORY',
            'data' => $uniqueProductsIds,
            'user_id' => Str::uuid()->toString(),
            'company_id' => Company::COMPANY_CCXC,
        ])["data"];
    }
}
