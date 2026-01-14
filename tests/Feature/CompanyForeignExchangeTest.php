<?php

namespace Tests\Feature;

use App\Models\CompanyForeignExchange;
use App\Models\Company;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Provider\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CompanyForeignExchangeTest extends TestCase
{
    use RefreshDatabase;


    /**
     * @var Company
     */
    private $company;

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::factory()->create();
        Membership::factory()->create([
            'company_id' => $this->company->id
        ]);
    }

    /** @test */
    public function it_should_create_foreign_exchange()
    {
        $this->initTestData();
        $data = [
            'foreign_exchange_id' => Str::uuid()->toString(),
            'company_id' => $this->company->id,
            'is_active' => true
        ];
        $response = $this->post('/api/company/companies-foreign-exchange', $data);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, CompanyForeignExchange::all());
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
    }

    /** @test */
    public function it_should_get_foreign_exchange()
    {
        $this->initTestData();
        CompanyForeignExchange::factory(3)->create(['foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::AFN], 'company_id' => Company::COMPANY_CCXC, 'is_active' => false]);
        CompanyForeignExchange::factory(3)->create(['foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::AFN], 'company_id' => Company::COMPANY_CCXC, 'is_active' => true]);
        $response = $this->post('/api/company/companies-foreign-exchange/list/'.Company::COMPANY_CCXC, ['is_active' => [true]]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
    }

    /** @test */
    public function it_should_update_foreign_exchange()
    {
        $this->initTestData();
        $foreignExchange = CompanyForeignExchange::factory()->create(['company_id' => Company::COMPANY_CCXC, 'is_active' => true]);
        $data = [
            'foreign_exchange_id' => $foreignExchange->foreign_exchange_id,
            'company_id' => Company::COMPANY_CCXC,
            'is_active' => false,
        ];
        $response = $this->put('/api/company/companies-foreign-exchange/'.$foreignExchange->id, $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
    }

    /** @test */
    public function it_should_delete_foreign_exchange()
    {
        $this->initTestData();
        $foreignExchange = CompanyForeignExchange::factory()->create(['foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP], 'company_id' => Company::COMPANY_CCXC, 'is_active' => true]);
        $foreignExchangeTwo = CompanyForeignExchange::factory()->create(['foreign_exchange_id' => Str::uuid()->toString(), 'company_id' => Company::COMPANY_CCXC, 'is_active' => true]);
        $foreignExchangeTree = CompanyForeignExchange::factory()->create(['foreign_exchange_id' => Str::uuid()->toString(), 'company_id' => Company::COMPANY_CCXC, 'is_active' => true]);
        $response = $this->delete('/api/company/companies-foreign-exchange/many/'.Company::COMPANY_CCXC, [$foreignExchange->id, $foreignExchangeTwo->id, $foreignExchangeTree->id]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, CompanyForeignExchange::all());
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
    }
}
