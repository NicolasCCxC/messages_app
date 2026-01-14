<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CompanyInformationControllerTest extends TestCase
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
    public function it_should_add_store_company_information()
    {
        $data = [
            'company_id' => $this->headers['company-id']
        ];

        $response = $this->postJson('/pays/company-set-up', $data, $this->headers);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $this->assertDatabaseHas('company_information', [
            'company_id' => $this->headers['company-id']
        ]);

        $response->assertJsonFragment([
            'company_id' => $this->headers['company-id']
        ]);
    }

    /** @test */
    public function it_should_get_information()
    {
        $data = [
            'company_id' => $this->headers['company-id']
        ];
        $this->postJson('/pays/company-set-up', $data, $this->headers);

        $response = $this->getJson('/pays/company-set-up', $this->headers);

        $response->assertJsonFragment([
            'company_id' => $this->headers['company-id']
        ]);
    }
}
