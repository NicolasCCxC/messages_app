<?php

namespace App\Enums;

Enum Payment
{
    public const APPROVED = 'APPROVED';
    public const DECLINED = 'DECLINED';
    public const PENDING = 'PENDING';
    public const EXPIRED = 'EXPIRED';
    public const ERROR = 'ERROR';

    public const METHOD_CASH = 'PAYMENT_METHOD_CASH';
    public const METHOD_PSE = 'PAYMENT_METHOD_PSE';
    public const METHOD_CREDIT_CARD = 'PAYMENT_METHOD_CREDIT_CARD';

    public const LIST = [
        self::METHOD_CASH => 'dd87a4a7-e24e-402c-89b2-664298173882',
        self::METHOD_PSE => 'c4024910-a753-43a1-995d-e927c5846e6a',
        self::METHOD_CREDIT_CARD => 'cccfbc34-3a42-4045-8833-6cba22b28ad9',
    ];

    public const STATUS = [
        self::APPROVED => 'APPROVED',
        self::DECLINED => 'DECLINED',
        self::PENDING => 'PENDING',
        self::EXPIRED => 'EXPIRED',
        self::ERROR => 'ERROR'
    ];
}
