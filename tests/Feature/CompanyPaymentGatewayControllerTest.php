<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CompanyPaymentGatewayControllerTest extends TestCase
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

    /** @test  */
    public function it_should_store()
    {

        $data = [
            'company_id' => $this->headers['company-id']
        ];

        $companyInformation = $this->postJson('/pays/company-set-up', $data, $this->headers);

        $data = [
            'payment_gateway_id' => 1,
            'credentials' => [
                'apiLogin' => 'aqui se supone que va la api login',
                'apiKey' => 'aqui se supone que va la api key',
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
    }
}
