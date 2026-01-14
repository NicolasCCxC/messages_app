<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use App\Models\Prefix;
use Carbon\Carbon;
use Illuminate\Support\Str;

class NotificationHelper
{
    /**
     * @throws GuzzleException
     */
    public static function storeNotification(Prefix $prefix, string $type)
    {
        $electonicNotification = [
            'type' => 'ELECTRONIC_INVOICE',
            'reference' => $prefix->id,
            'consecutive' => $prefix->resolution_number,
            'module_notification_id' => '641a18d5-baa9-35ff-a2ae-daa86cdb8363', //electronic invoice
            'date' => Carbon::now(),
            'user_id' => '752b29e6-baa9-35ff-a2ae-daa86cdb9474',
            'company_id' => $prefix->company_id,
            'type_notification_id' => $type,
            'state_notification_id' => 'a157b0b8-b0bf-36b5-b1bb-10f82deeaec4', //send
            'description' => 'Resolución ' . $prefix->resolution_number,
        ];

        $request = [
            'method' => 'POST',
            'service' => 'NOTIFICATION',
            'resource' => '/notifications',
            'data' => $electonicNotification,
            'user_id' => Str::uuid()->toString(),
            'company_id' => $prefix->company_id,
        ];
        try {
            return GatewayHelper::routeHandler($request)['data'];
        } catch (\Exception $exception) {
        }
    }
}
