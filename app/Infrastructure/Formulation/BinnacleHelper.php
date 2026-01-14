<?php

namespace App\Infrastructure\Formulation;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class BinnacleHelper
{
    /**
     * @throws GuzzleException
     */
    public static function internalActivity(string $ip, string $userId, string $name, string $email, string $companyId, string $module, string $register)
    {
        $activity = [
            'company_id' => $companyId,
            'date' => Carbon::now()->getTimestamp(),
            'ip' => $ip,
            'activity' => $register,
            'user' => [
                'id' => $userId,
                'name' => $name,
                'email' => $email
            ],
            'module' => [
                'id' => Str::uuid()->toString(),
                'name' => $module
            ]
        ];

        return GatewayHelper::routeHandler([
                'resource' => '/internalActivities',
                'method' => 'POST',
                'service' => 'BINNACLE',
                'data' => $activity,
                'user_id' => $userId,
                'company_id' => $companyId,
            ])['data'] ?? [];
    }
}
