<?php


namespace App\Infrastructure\Formulation;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MembershipHelper
{
    /**
     * @param array $ids example [1,2,3,4,5]
     * @return Collection
     * @throws GuzzleException
     */
    public static function getMembershipModulesByIds(array $ids)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/utils/membership-modules',
            'method' => 'POST',
            'service' => 'UTILS',
            'data' => $ids,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ])['data'];
    }

    /**
     * @throws GuzzleException
     */
    public static function getAllMembershipModules()
    {
        return GatewayHelper::routeHandler([
            'resource' => '/utils/membership-modules',
            'method' => 'GET',
            'service' => 'UTILS',
            'data' => [],
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ])['data'];
    }
    /**
     *
     * @return Collection
     * @throws GuzzleException
     */
    public static function getAllMembershipModulesByArrayIds(array $arrayIds)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/utils/membership-modules/get-modules-by-ids',
            'method' => 'POST',
            'service' => 'UTILS',
            'data' => $arrayIds,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ])['data'];
    }

    /**
     * @throws GuzzleException
     */
    public static function getAllDocumentTypes()
    {
        return GatewayHelper::routeHandler([
            'resource' => '/utils/document-types',
            'method' => 'GET',
            'service' => 'UTILS',
            'data' => [],
            'user_id' => Str::uuid()->toString(),
            'company_id' => Str::uuid()->toString(),
        ])['data'];
    }
}
