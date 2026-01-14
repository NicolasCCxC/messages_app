<?php

namespace App\Enums;

class PaymentMethodsEnum
{
    public const PSE = 'PSE';
    public const CREDIT_CARD = 'CREDIT_CARD';
    public const DEBIT_CARD = 'DEBIT_CARD';

    public const DESCRIPTIONS = [
        self::PSE => 'Consignación bancaria',
        self::CREDIT_CARD => 'Tarjeta Crédito',
        self::DEBIT_CARD => 'Tarjeta Débito',
    ];

}
