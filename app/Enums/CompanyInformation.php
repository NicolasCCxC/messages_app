<?php

namespace App\Enums;

Enum CompanyInformation
{
    public const PAYMENT_INFORMATION = [
        "language" => "es",
        "command" => "SUBMIT_TRANSACTION",
        "transaction" => [
            "order" => [
                "accountId" => "{{account_co}}",
                "referenceCode" => "{{reference_code}}",
                "description" => "Payment test description",
                "language" => "es",
                "signature" => "{{signature}}",
                "notifyUrl" => "{{confirmation_page}}",
                "additionalValues" => [
                    "TX_VALUE" => [
                        "value" => 0,
                        "currency" => "{{currency_co}}"
                    ],
                    "TX_TAX" => [
                        "value" => 0,
                        "currency" => "{{currency_co}}"
                    ],
                    "TX_TAX_RETURN_BASE" => [
                        "value" => 0,
                        "currency" => "{{currency_co}}"
                    ]
                ],
                "buyer" => [
                    "merchantBuyerId" => "1",
                    "fullName" => "First name and second buyer name",
                    "emailAddress" => "buyer_test@test.com",
                    "contactPhone" => "7563126",
                    "dniNumber" => "123456789",
                    "shippingAddress" => [
                        "street1" => "calle 100",
                        "street2" => "5555487",
                        "city" => "Medellin",
                        "state" => "Antioquia",
                        "country" => "CO",
                        "postalCode" => "000000",
                        "phone" => "7563126"
                    ]
                ],
                "shippingAddress" => [
                    "street1" => "calle 100",
                    "street2" => "5555487",
                    "city" => "Medellin",
                    "state" => "Antioquia",
                    "country" => "CO",
                    "postalCode" => "0000000",
                    "phone" => "7563126"
                ]
            ],
            "payer" => [
                "merchantPayerId" => "1",
                "fullName" => "First name and second payer name",
                "emailAddress" => "payer_test@test.com",
                "contactPhone" => "7563126",
                "dniNumber" => "5415668464654",
                "billingAddress" => [
                    "street1" => "calle 93",
                    "street2" => "125544",
                    "city" => "Bogota",
                    "state" => "Bogota DC",
                    "country" => "CO",
                    "postalCode" => "000000",
                    "phone" => "7563126"
                ]
            ],
            "creditCardTokenId" => "{{token}}",
            "creditCard" => [
                "processWithoutCvv2" => false,
            ],
            "extraParameters" => [
                "INSTALLMENTS_NUMBER" => 1
            ],
            "type" => "AUTHORIZATION_AND_CAPTURE",
            "paymentMethod" => "VISA",
            "paymentCountry" => "CO",
            "deviceSessionId" => "vghs6tvkcle931686k1900o6e1",
            "ipAddress" => "127.0.0.1",
            "cookie" => "pt1t38347bs6jc9ruv2ecpv7o2",
            "userAgent" => "Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
        ],
        "test" => true
    ];
}