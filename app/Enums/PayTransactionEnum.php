<?php

namespace App\Enums;

class PayTransactionEnum
{
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_FAILED   = 'FAILED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_PENDING  = 'PENDING';

    const STATUS_MESSAGES = [
        self::STATUS_APPROVED => 'Aprobada',
        self::STATUS_FAILED   => 'Fallida',
        self::STATUS_REJECTED => 'Rechazada',
        self::STATUS_PENDING  => 'Pendiente',
    ];

    const STATUS_CODES = [
        self::STATUS_APPROVED => 1,
        self::STATUS_FAILED   => 5,
        self::STATUS_REJECTED => 4,
        self::STATUS_PENDING  => 25,
    ];
}