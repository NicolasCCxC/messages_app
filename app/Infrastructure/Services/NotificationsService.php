<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Formulation\GatewayHelper;
use App\Traits\CommunicationBetweenServicesTrait;
use Illuminate\Support\Str;

class NotificationsService
{
    use CommunicationBetweenServicesTrait;

    public function sendEmailForMembershipFinished(array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/notifications/send-email-membership-finished',
            'method' => 'POST',
            'service' => 'NOTIFICATION',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => $data['company_id'] ?? Str::uuid()->toString(),
        ]);
    }

    public function sendEmailSaleMembership(array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/notifications/sale-membership',
            'method' => 'POST',
            'service' => 'NOTIFICATION',
            'data' => $data,
            'user_id' => Str::uuid()->toString(),
            'company_id' => $data['company_id'],
        ]);
    }

    /**
     *
     * @param array $data
     * @return JsonResponse
     */
    public function sendNotification(array $data, string $companyId)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/notifications',
            'method' => 'POST',
            'service' => 'NOTIFICATION',
            'data' => $data,
            'user_id' => Str::uuid()->toString(),
            'company_id' => $companyId,
        ]);
    }
}
