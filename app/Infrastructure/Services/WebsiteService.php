<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Formulation\GatewayHelper;
use App\Traits\CommunicationBetweenServicesTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;


class WebsiteService
{
    use CommunicationBetweenServicesTrait;

    /**
     * @param array $arrayIds
     * @return array
     *
     * @service WEBSITE /websites/company-logo
     *
     * @throws GuzzleException
     */
    public function getCompanyLogoByDomain(array $data): array
    {
        return GatewayHelper::routeHandler([
            'resource' => '/websites/company-logo',
            'method' => 'POST',
            'service' => 'WEBSITE',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ])['data'] ?? null;
    }
}
