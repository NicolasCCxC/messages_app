<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Formulation\GatewayHelper;
use App\Traits\CommunicationBetweenServicesTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;


class UtilsService
{
    use CommunicationBetweenServicesTrait;

    /**
     * @param array $arrayIds
     * @return array
     *
     * @service UTILS /utils/membership-modules/get-submodules-by-id
     *
     * @throws GuzzleException
     */
    public function getSubModulesById(array $arrayIds): array
    {
        return GatewayHelper::routeHandler([
            'resource' => '/utils/membership-modules/get-submodules-by-id',
            'method' => 'POST',
            'service' => 'UTILS',
            'data' => $arrayIds,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ])['data'];
    }
}
