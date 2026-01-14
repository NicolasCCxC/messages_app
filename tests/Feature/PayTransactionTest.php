<?php

namespace Tests\Feature;

use App\Models\PayTransaction;
use App\Models\Membership;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PayTransactionTest extends TestCase
{
    use RefreshDatabase;

    private $company;
    private $membership;

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::factory()->create();
        $this->membership = Membership::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_get_transactions_by_membership()
    {
        $this->initTestData();

        PayTransaction::factory()->count(2)->create([
            'membership_id' => $this->membership->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->json('GET','/api/company/memberships/transactions/membership/' . $this->membership->id, [], $this->servers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                '*' => [
                    'id',
                    'transaction_id',
                    'membership_id',
                    'company_id',
                    'users_quantity',
                    'invoices_quantity',
                    'pages_quantity',
                    'status',
                    'json_invoice',
                    'json_pse_url_response',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /** @test */
    public function it_should_get_transactions_by_company()
    {
        $this->initTestData();

        PayTransaction::factory()->count(2)->create([
            'membership_id' => $this->membership->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->json('GET', '/api/company/memberships/transactions/company/' . $this->company->id, [], $this->servers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                '*' => [
                    'id',
                    'transaction_id',
                    'membership_id',
                    'company_id',
                    'users_quantity',
                    'invoices_quantity',
                    'pages_quantity',
                    'status',
                    'json_invoice',
                    'json_pse_url_response',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /** @test */
    public function it_should_update_json_pse_url_response()
    {
        $this->initTestData();

        $transaction = PayTransaction::factory()->create([
            'company_id' => $this->company->id,
            'membership_id' => $this->membership->id,
            'json_pse_url_response' => null,
        ]);
        $payload = [
            'json_pse_url_response' => [
                'company_id' => $this->company->id,
                'is_immediate_purchase' => false,
                'users_quantity' => 0,
                'pages_quantity' => 0,
                'modules' => [
                    [
                        'id' => 3,
                        'sub_modules' => [
                            ['id' => 2, 'expiration_date' => 12],
                        ],
                        'expiration_date' => 12,
                    ],
                    [
                        'id' => 2,
                        'sub_modules' => [
                            ['id' => 10, 'expiration_date' => 12],
                        ],
                        'expiration_date' => 12,
                    ],
                    [
                        'id' => 5,
                        'sub_modules' => [],
                        'expiration_date' => 12,
                    ],
                ],
                'additional_customer_data' => [
                    'type_taxpayer_id' => 'fake-taxpayer-id',
                    'type_taxpayer_name' => 'Persona natural',
                    'tax_details_code' => 'ZZ',
                    'tax_details_name' => 'No aplica',
                    'document_type' => 'fake-document-type-id',
                    'department_id' => 15,
                    'country_id' => 46,
                    'city_id' => '999',
                    'fiscal_responsibilities' => [
                        ['id' => 5],
                    ],
                ],
                'payu_data' => [
                    'transaction' => [
                        'order' => [
                            'description' => 'Pago de membresía en diggi pymes:',
                            'notifyUrl' => 'https://example.com/notify',
                            'buyer' => [
                                'fullName' => 'Cliente Prueba',
                                'emailAddress' => 'cliente@example.com',
                                'contactPhone' => '3000000000',
                                'dniNumber' => '123456789',
                                'shippingAddress' => [
                                    'street1' => 'Calle Falsa 123',
                                    'street2' => 'Apto 4',
                                    'city' => 'Ciudad Ejemplo',
                                    'state' => 'Estado Ejemplo',
                                    'country' => 'CO',
                                    'postalCode' => '000000',
                                    'phone' => '3000000000',
                                ],
                            ],
                        ],
                        'payer' => [
                            'fullName' => 'Cliente Prueba',
                            'emailAddress' => 'cliente@example.com',
                            'contactPhone' => '3000000000',
                            'dniNumber' => '123456789',
                            'billingAddress' => [
                                'street1' => 'Calle Falsa 123',
                                'street2' => 'Apto 4',
                                'city' => 'Ciudad Ejemplo',
                                'state' => 'Estado Ejemplo',
                                'country' => 'CO',
                                'postalCode' => '000000',
                                'phone' => '3000000000',
                            ],
                        ],
                        'paymentMethod' => 'PSE',
                        'userAgent' => 'TestAgent/1.0',
                        'extraParameters' => [
                            'RESPONSE_URL' => 'https://example.com/summary-pay-pse',
                            'PSE_REFERENCE1' => '127.0.0.1',
                            'FINANCIAL_INSTITUTION_CODE' => '1022',
                            'USER_TYPE' => 'N',
                            'PSE_REFERENCE2' => 'CC',
                            'PSE_REFERENCE3' => '123456789',
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->putJson('/api/company/memberships/transactions/' . $transaction->company_id . '/pse-response', $payload, $this->servers);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'id',
                'transaction_id',
                'membership_id',
                'company_id',
                'users_quantity',
                'invoices_quantity',
                'pages_quantity',
                'status',
                'json_invoice',
                'json_pse_url_response',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals($payload['json_pse_url_response'], $response->json('data.json_pse_url_response'));
    }

      /** @test */
    public function it_should_validate_pay_transactions_by_membership()
    {
        $this->initTestData();

        PayTransaction::factory()->create([
            'membership_id' => $this->membership->id,
            'company_id' => $this->company->id,
            'status' => PayTransaction::PAYMENT_STATUS_PENDING,
            'transaction_id' => '06dfa518-224c-44cd-b1d1-a332fa538542',
        ]);

        $this->artisan('validate:pay-transaction');
        $this->assertDatabaseHas('pay_transactions',[
           'status' => PayTransaction::PAYMENT_STATUS_APPROVED,
       ]);
    }
}