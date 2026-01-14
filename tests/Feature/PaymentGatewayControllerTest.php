<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PaymentGatewayControllerTest extends TestCase
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
    public function it_should_get_payment_gateways()
    {
        $response = $this->getJson('/pays/payment-gateway', $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonFragment([
            "name" => "PayU"
        ]);
    }
}
