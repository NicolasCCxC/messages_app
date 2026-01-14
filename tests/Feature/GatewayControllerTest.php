<?php

namespace Tests\Feature;

use App\Models\Payment;
use Faker\Provider\Uuid;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Tests\TestCase;

class GatewayControllerTest extends TestCase
{
    use DatabaseMigrations, InteractsWithExceptionHandling, DatabaseTransactions;

    private $headers;


    public function setUp(): void
    {
        parent::setUp();
        $this->headers = [
            'Authorization' => 'Bearer ' . env('SERVICE_TOKEN',''),
            'company-id' => 'e77a1cf6-3d8f-3d45-96ce-0132c3bb771f',
            'user-id' => 'ac171386-f7eb-411f-875d-8aa60cb4cc18'
        ];
    }

    /** @test */
    public function it_should_credit_card_transfer()
    {
        $data = [
            'company_id' => $this->headers['company-id']
        ];

        $companyInformation = $this->postJson('/pays/company-set-up', $data, $this->headers);

        $data = [
            'payment_gateway_id' => 1,
            'credentials' => [
                'api_login' => 'pRRXKOl8ikMmt9u',
                'api_key' => '4Vj8eK4rloUd272L48hsrarnUA',
                'merchant_id' => '508029',
                'public_key' => 'PKaC6H4cEDJD919n705L544kSU	',
                'account_id' => '512321',
            ],
            'date' => (string)Carbon::now()->getTimestamp(),
            'company_information_id' => $companyInformation->json('data')['id']
        ];


        $response = $this->postJson('/pays/company-payments', $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $this->assertDatabaseHas('company_payment_gateways',
        [
            'id' => $response->json('data')[0]['id'],
            'payment_gateway_id' => 1
        ]);

        $data = [
            'ip' => '127.0.0.1',
            'user_agent' => 'Mozilla\/5.0 (Windows NT 5.1; rv:18.0) Gecko\/20100101 Firefox\/18.0',
            'url' => 'ccxc-diggy.diggiefi.com',
            'payer' => [
                "email_address" => "lgonzalez@ccxc.us",
                "full_name" => "Luis Gonzalez",
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
            'credit_card' => [
                'number' => '4037997623271984',
                'security_code' => '777',
                'expiration_date' => '2026/05',
                'name' => 'APPROVED',
                'dues' => '1',
            ],
            'shopping_cart' => [
                'id' => Uuid::uuid(),
                'total_value' => 150.22,
                'tax_value' => 14.4
            ],
            'buyer' => [
                'full_name' => 'Luis Gonzalez',
                'email_address' => 'lgonzalez@ccxc.us',
                'contact_phone' => '3016713769',
                'dni_number' => '1069759295'
            ],
            'shipping_address' => [
                'street1'=> 'Cr 23 No. 53-50',
                'street2'=> '5555487',
                'city'=> 'Bogotá',
                'state'=> 'Bogotá D.C.',
                'country'=> 'CO',
                'postal_code'=> '000000',
                'phone'=> '7563126'
            ]
        ];
        $response = $this->postJson("/pays/gateway/credit-card-transfer/1", $data, $this->headers);
        $reference = $response->getOriginalContent()['data']['transactionResponse']['transactionId'];
        $clientId = Payment::select('client_id')->where('reference', $reference)->first();
        $this->getJson("/pays/payment/{$clientId->client_id}", $this->headers);

        $this->assertDatabaseHas('payments',
        [
            'reference' => $reference,
            'client_id' => $clientId->client_id
        ]);
    }

}
