<?php

namespace App\Enums;

use Carbon\Carbon;

enum NotificationTypeEnum: string
{
    public const PURCHASE_ORDER = 'PURCHASE_ORDER';
    public const MODULE_NOTIFICATIONS = 'd7fb18df-d4f0-4257-bba9-efcefe5fd10e';

    public const NOTIFICATION_STATES = [
        'PENDING' => '1cc36b00-2b46-36cd-b306-4bff0a438baa',
        'SENT' => 'a157b0b8-b0bf-36b5-b1bb-10f82deeaec4',
    ];

    case ACCEPTED_PURCHASE_ORDER = 'b8ab3d53-c6a1-4923-9eca-438ae62fe587';
    case PENDING_PURCHASE_ORDER = 'b083d645-5cfa-4dce-89da-78704b8d1aa2';
    case EXCEEDED_PAYMENT_TIME = '92b4280e-e09c-4e65-a565-53573ca4818d';

    public static function fromId(string $id): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $id) {
                return $case;
            }
        }
        return null;
    }

    public function getDetails(string $invoiceNumber = null, string $clientName = null, string $date = null): array
    {
        $date = $date ?? Carbon::now('America/Bogota')->format('d/m/Y');
        return match($this) {
            self::ACCEPTED_PURCHASE_ORDER => [
                'type' => $this->value,
                'description' => "<div>La orden de compra <span class='font-allerbold'>#{$invoiceNumber} </span> fue aprobada y pagada con éxito el día <span class='font-allerbold'>{$date} </span></div>",
                'title' => "Compra aprobada:",
            ],
            self::PENDING_PURCHASE_ORDER => [
                'type' => $this->value,
                'description' => "<div>La orden de compra <span class='font-allerbold'>#{$invoiceNumber} </span> fue realizada por el cliente <span class='font-allerbold'>{$clientName} </span> y tiene un estado pendiente</div>",
                'title' => "Compra realizada estado pendiente:",
            ],
            self::EXCEEDED_PAYMENT_TIME => [
                'type' => $this->value,
                'description' => "<div>La orden de compra <span class='font-allerbold'>#{$invoiceNumber} </span> expiró el día <span class='font-allerbold'>{$date} </span> debido a que excedió el tiempo de pago para su orden compra.</div>",
                'title' => "Cancelación orden de pago:",
            ],
        };
    }
}
