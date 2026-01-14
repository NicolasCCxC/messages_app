<?php

namespace Tests\Feature;

use App\Enums\CompanyInformation as EnumsCompanyInformation;
use App\Helpers\Utils;
use App\Models\CompanyPaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MembershipPaymentControllerTest extends TestCase
{
    use DatabaseMigrations;

    private $headers = [
//            'Authorization' => 'Bearer ' . env('SERVICE_TOKEN'),
        'user-id' => '4e999247-6529-4cdb-a668-f2922889c304',
        'company-id' => '83e80ae5-affc-32b4-b11d-b4cab371c48b'
    ];

    private $dataRecurringPaymentRegistration = [
        "transaction" => [
            "order" => [
                "description" => "Payment test description",
                "notifyUrl" => "http://www.payu.com/notify",
                "additionalValues" => [
                    "TX_VALUE" => [
                        "value" => 65000,
                        "currency" => "COP"
                    ],
                    "TX_TAX" => [
                        "value" => 10378,
                        "currency" => "COP"
                    ],
                    "TX_TAX_RETURN_BASE" => [
                        "value" => 54622,
                        "currency" => "COP"
                    ]
                ],
                "buyer" => [
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
            ],
            "payer" => [
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
            "creditCard" => [
                "payerId" => "12",
                "name" => "APPROVED",
                "identificationNumber" => "32144457",
                "paymentMethod" => "VISA",
                "number" => "4037997623271984",
                "expirationDate" => "2027/01",
                "securityCode" => "123",
            ],
            "paymentMethod" => "VISA",
            "ipAddress" => "127.0.0.1",
            "userAgent" => "Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
        ]
    ];

    /** @test */
    public function should_test_ping_payu()
    {
        $response = Utils::ping_payu();
        $this->assertTrue($response);
    }

    /** @test */
    public function should_create_credit_card_token_id_payu()
    {
        CompanyPaymentGateway::factory()->create();
        $data = [
            "language" => "es",
            "command" => "CREATE_TOKEN",
            "creditCardToken" => [
                "payerId" => "10",
                "name" => "APPROVED",
                "identificationNumber" => "32144457",
                "paymentMethod" => "VISA",
                "number" => "4037997623271984",
                "expirationDate" => "2027/01",
                "securityCode" => "123",
            ]
        ];

        $response = $this->postJson("/pays/membership/get-card-token", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'id',
                'company_id',
                'payment_information',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    /** @test */
    public function should_save_data_for_recurring_payment_registration()
    {
        CompanyPaymentGateway::factory()->create();

        $data = $this->dataRecurringPaymentRegistration;

        $response = $this->postJson("/pays/membership/recurring-payment-registration", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'transactionResponse' => [
                    'orderId',
                    'transactionId',
                    'state',
                    'paymentNetworkResponseCode',
                    'paymentNetworkResponseErrorMessage',
                    'trazabilityCode',
                    'authorizationCode',
                    'pendingReason',
                    'responseCode',
                    'responseMessage',
                    'transactionDate',
                    'transactionTime',
                    'operationDate',
                    'extraParameters' => [
                        'BANK_REFERENCED_CODE',
                    ],
                    'additionalInfo' => [
                        'paymentNetwork',
                        'rejectionType',
                        'responseNetworkMessage',
                        'travelAgencyAuthorizationCode',
                        'cardType',
                        'cardType',
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_delete_credit_card_token_id_payu()
    {
        CompanyPaymentGateway::factory()->create();

        $data = $this->dataRecurringPaymentRegistration;

        $response = $this->postJson("/pays/membership/recurring-payment-registration", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->postJson("/pays/membership/delete-card-token", [], $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $this->assertDatabaseHas('company_information', [
            'company_id' => $this->headers['company-id'],
            'payment_information' => json_encode(EnumsCompanyInformation::PAYMENT_INFORMATION)
        ]);

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'creditCardToken' => [
                    'creditCardTokenId',
                    'name',
                    'creditCardTokenId',
                    'payerId',
                    'identificationNumber',
                    'paymentMethod',
                    'number',
                    'expirationDate',
                    'creationDate',
                    'maskedNumber',
                    'errorDescription',
                ]
            ]
        ]);
    }

    /** @test */
    public function should_make_pay_with_token()
    {
        CompanyPaymentGateway::factory()->create();

        $data = $this->dataRecurringPaymentRegistration;

        $response = $this->postJson("/pays/membership/recurring-payment-registration", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $data = [
            "TX_VALUE" => [
                "value" => 650000,
                "currency" => "COP"
            ],
            "TX_TAX" => [
                "value" => 103780,
                "currency" => "COP"
            ],
            "TX_TAX_RETURN_BASE" => [
                "value" => 546220,
                "currency" => "COP"
            ]
        ];
        $response = $this->postJson("/pays/membership/payment-with-token", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'transactionResponse' => [
                    'orderId',
                    'transactionId',
                    'state',
                    'paymentNetworkResponseCode',
                    'paymentNetworkResponseErrorMessage',
                    'trazabilityCode',
                    'authorizationCode',
                    'pendingReason',
                    'responseCode',
                    'responseMessage',
                    'transactionDate',
                    'transactionTime',
                    'operationDate',
                    'extraParameters' => [
                        'BANK_REFERENCED_CODE',
                    ],
                    'additionalInfo' => [
                        'paymentNetwork',
                        'rejectionType',
                        'responseNetworkMessage',
                        'travelAgencyAuthorizationCode',
                        'cardType',
                        'cardType',
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_make_pay_without_token()
    {
        CompanyPaymentGateway::factory()->create();

        $data = $this->dataRecurringPaymentRegistration;
        unset($data['transaction']['creditCard']['payerId']);
        unset($data['transaction']['creditCard']['paymentMethod']);
        unset($data['transaction']['creditCard']['identificationNumber']);

        $response = $this->json("POST", "/pays/membership/payment-without-token", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'transactionResponse' => [
                    'orderId',
                    'transactionId',
                    'state',
                    'paymentNetworkResponseCode',
                    'paymentNetworkResponseErrorMessage',
                    'trazabilityCode',
                    'authorizationCode',
                    'pendingReason',
                    'responseCode',
                    'responseMessage',
                    'transactionDate',
                    'transactionTime',
                    'operationDate',
                    'extraParameters' => [
                        'BANK_REFERENCED_CODE',
                    ],
                    'additionalInfo' => [
                        'paymentNetwork',
                        'rejectionType',
                        'responseNetworkMessage',
                        'travelAgencyAuthorizationCode',
                        'cardType',
                        'cardType',
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_get_card_payu()
    {
        CompanyPaymentGateway::factory()->create();

        $data = $this->dataRecurringPaymentRegistration;

        $response = $this->postJson("/pays/membership/recurring-payment-registration", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->getJson("/pays/membership/get-card-payu", $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'creditCardTokenId',
                'name',
                'payerId',
                'identificationNumber',
                'paymentMethod',
                'number',
                'expirationDate',
                'creationDate',
                'maskedNumber',
                'errorDescription',
            ]
        ]);
    }

    /** @test */
    public function should_get_banks_list()
    {
        $response = $this->getJson("/pays/membership/pse-banks", $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                [
                    'id',
                    'description',
                    'pseCode',
                ]
            ]
        ]);
    }

    /** @test */
    public function should_get_details_transaction_payu()
    {
        $data = [
            "transactionId" => "0c794165-4b89-4cea-ad7e-0c4b22811faa"
        ];
        $response = $this->postJson("/pays/membership/get-details-transaction", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'result' => [
                    'payload' => [
                        'state',
                        'paymentNetworkResponseCode',
                        'paymentNetworkResponseErrorMessage',
                        'trazabilityCode',
                        'authorizationCode',
                        'pendingReason',
                        'responseCode',
                        'errorCode',
                        'responseMessage',
                        'transactionDate',
                        'transactionTime',
                        'operationDate',
                        'extraParameters',
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_get_company_payu_data()
    {
        CompanyPaymentGateway::factory()->create();

        $data = $this->dataRecurringPaymentRegistration;

        $response = $this->postJson("/pays/membership/recurring-payment-registration", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->getJson("/pays/membership/get-payu-data/{$this->headers['company-id']}", $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'payu_data' => [
                    'transaction' => [
                        'order' => [],
                        'payer' => [],
                        'ipAddress',
                        'userAgent',
                        'creditCard' => [],
                        'paymentMethod',
                        'creditCardTokenId'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_pay_pse_payu()
    {
        $data = $this->dataRecurringPaymentRegistration;
        unset($data['transaction']['creditCard']);
        $data['transaction']['extraParameters'] = [
            "RESPONSE_URL" => "http://www.payu.com/notify",
            "PSE_REFERENCE1" => "127.0.0.1",
            "FINANCIAL_INSTITUTION_CODE" => "1022",
            "USER_TYPE" => "N",
            "PSE_REFERENCE2" => "CC",
            "PSE_REFERENCE3" => "123456789"
        ];
        $data['transaction']['paymentMethod'] = "PSE";

        $response = $this->postJson("/pays/membership/pse", $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'transactionResponse' => [
                    'orderId',
                    'transactionId',
                    'state',
                    'paymentNetworkResponseCode',
                    'paymentNetworkResponseErrorMessage',
                    'trazabilityCode',
                    'authorizationCode',
                    'pendingReason',
                    'responseCode',
                    'responseMessage',
                    'transactionDate',
                    'transactionTime',
                    'operationDate',
                    'extraParameters' => [
                        'TRANSACTION_CYCLE',
                        'BANK_URL',
                    ],
                    'additionalInfo' => [
                        'paymentNetwork',
                        'rejectionType',
                        'responseNetworkMessage',
                        'travelAgencyAuthorizationCode',
                        'cardType',
                        'cardType',
                    ]
                ]
            ]
        ]);
    }


    /*

    public function should_test_all_endpoints()
    {
        //get pse banks

        $pseBanks = $this->get('/pays/membership/pse-banks');

        $pseBanks->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                [
                    'id',
                    'description',
                    'pseCode'
                ]
            ]
        ]);

        $bank = $pseBanks->response->json('data')[1]['pseCode'];

        $data = [
            'invoice' => [
                'id' => Uuid::uuid(),
                'total_value' => 150.22,
                'tax_value' => 14.4
            ],
            'buyer' => [
                'full_name' => 'Nicolas Sabogal Torres',
                'email_address' => 'nsabogal@ccxc.us',
                'contact_phone' => '3016713769',
                'dni_number' => '1069759295'
            ],
            'shipping_address' => [
                'street1' => 'Cr 23 No. 53-50',
                'street2' => '5555487',
                'city' => 'Bogotá',
                'state' => 'Bogotá D.C.',
                'country' => 'CO',
                'postal_code' => '000000',
                'phone' => '7563126'
            ],
            'payer' => [
                "email_address" => "nsabogal@ccxc.us",
                "full_name" => "Nicolas Sabogal Torres",
                "contact_phone" => "3016713769",
                "dni_number" => "1069759295"
            ],
            'billing_address' => [
                "street1" => "Cr 23 No. 53-50",
                "street2" => "5555487",
                "city" => "Bogotá",
                "state" => "Bogotá D.C.",
                "country" => "CO",
                "postal_code" => "000000",
                "phone" => "7563126"
            ],
            'pse' => [
                "code" => "1039",
                "user_type" => "N",
                "user_document_type" => "CC",
                "user_document" => "1069759299"
            ],
            "ip" => "127.0.0.1",
            "user_agent" => "Mozilla\/5.0 (Windows NT 5.1; rv:18.0) Gecko\/20100101 Firefox\/18.0"
        ];

        $pseTransfer = $this->post('/pays/membership/pse', $data);

        $pseTransfer->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'code',
                'error',
                'transactionResponse' => [
                    "orderId" ,
                    "transactionId" ,
                    "state" ,
                    "paymentNetworkResponseCode",
                    "paymentNetworkResponseErrorMessage" ,
                    "trazabilityCode",
                    "authorizationCode" ,
                    "pendingReason" ,
                    "responseCode" ,
                    "errorCode" ,
                    "responseMessage" ,
                    "transactionDate" ,
                    "transactionTime",
                    "operationDate" ,
                    "referenceQuestionnaire",
//                    "extraParameters" =>  [ //this is to payu server
//                        "BANK_URL",
//                    ],
                    "additionalInfo" => [
                        "paymentNetwork" ,
                        "rejectionType" ,
                        "responseNetworkMessage",
                        "travelAgencyAuthorizationCode",
                        "cardType" ,
                        "transactionType" ,
                    ]
                ]
            ] //this is to server payu test is with issues
        ]);

        // transaction $id

        $transaction = $pseTransfer->response->json('data')['transactionResponse']['transactionId'];

        $status = $this->post('/pays/membership/report/'.$transaction,[]);

        $status->assertJsonStructure([
            "message",
            "statusCode",
            "service" ,
            "data" => [
                "code",
                "error",
                "result" => [
                    "payload" => [
                        "state",
                        "paymentNetworkResponseCode" ,
                        "paymentNetworkResponseErrorMessage" ,
                        "trazabilityCode",
                        "authorizationCode" ,
                        "pendingReason" ,
                        "responseCode" ,
                        "errorCode" ,
                        "responseMessage" ,
                        "transactionDate" ,
                        "transactionTime",
                        "operationDate" ,
                        "extraParameters" ,
                    ]
                ]
            ]
        ]);
    }

    */
}
