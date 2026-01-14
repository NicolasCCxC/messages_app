<?php

namespace App\Infrastructure\Services;

use App\Traits\CommunicationBetweenServicesTrait;
use App\Infrastructure\Formulation\GatewayHelper;
use Illuminate\Support\Str;

class BinnacleService
{
    use CommunicationBetweenServicesTrait;

    public function storeLastModifications (array $data)
    {
        return collect($this->makeRequest('POST', 'BINNACLE', '/internalActivities/', $data));
    }
}
