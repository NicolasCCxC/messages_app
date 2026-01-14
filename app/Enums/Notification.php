<?php

namespace App\Enums;

use Carbon\Carbon;

Enum Notification: string
{
    // Identifier for the notification type
    public const NOTIFICATION_TYPE = 'PAYMENT_PLANS';

    // Identifier for the notification type related to membership purchases
    public const NOTIFICATION_TYPE_MEMBERSHIP_PURCHASE = '2f3e3247-349e-4f76-a9e4-5c6bb42b302a';

    // Identifier for the module associated with payment plans
    public const MODULE_PAYMENT_PLANS = '01f960d6-af67-4c42-afdb-5f8f91a1bb9a';

    // Identifier for the state of a notification when it has been sent
    public const STATE_NOTIFICATION_SEND = '1cc36b00-2b46-36cd-b306-4bff0a438baa';

    // Identifier for the module of a notification when is electronic invoice
    public const ELECTRONIC_INVOICE_NOTIFICATION_MODULE = '641a18d5-baa9-35ff-a2ae-daa86cdb8363';

    case RESOLUTION_EXPIRATION_NOTIFICATION = PrefixEnum::RESOLUTION_EXPIRATION_NOTIFICATION;
    case RANK_DEPLETION_NOTIFICATION = PrefixEnum::RANK_DEPLETION_NOTIFICATION;

    public static function fromId(string $id): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $id) {
                return $case;
            }
        }
        return null;
    }

    public function getDetails(string $invoiceType = null, string $finalValidity = null, $resolutionNumber = null): array
    {
        $finalValidity = Carbon::parse($finalValidity)->format('Y-m-d');
        $daysDifference = ceil(Carbon::now()->diffInDays($finalValidity));

        $invoiceType = isset(PrefixEnum::TYPE_RESOLUTION_TRANSLATE[$invoiceType]) ? PrefixEnum::TYPE_RESOLUTION_TRANSLATE[$invoiceType] : null;

        return match($this) {
            self::RESOLUTION_EXPIRATION_NOTIFICATION => [
                'description' => "<div>La resolución de {$invoiceType}, está a <span class='font-allerbold'>{$daysDifference} días</span> de vencerse. Genere una nueva resolución en la DIAN.</div>",
                'title' => "Vencimiento de resolución:",
            ],
            self::RANK_DEPLETION_NOTIFICATION => [
                'description' => "<div>Resolución {$resolutionNumber}</div>",
                'title' => "Resolución:",
            ],
        };
    }

}
