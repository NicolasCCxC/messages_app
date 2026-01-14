<?php

namespace Tests\Feature;

use App\Infrastructure\Services\InvoiceService;
use App\Models\Company;
use App\Models\Membership;
use App\Models\Prefix;
use Faker\Provider\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class PrefixTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_store_prefix_physical_store_website()
    {
        $this->initTestData();
        $prefix = Prefix::factory(1)->create(['type' => Prefix::INVOICE, 'company_id' => $this->company->id, 'contingency' => false]);
        $data = [
            [
                "id" =>  $prefix[0]->id,
                'resolution_technical_key' => Uuid::uuid(),
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::INVOICE,
                'prefix' => 'FE',
                'initial_validity' => '18-11-2021 07:00:00',
                'final_validity' => '18-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => true,
                'website' => true,
                'contingency' => false
            ],
            [
                'resolution_technical_key' => Uuid::uuid(),
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::INVOICE,
                'prefix' => 'CEO',
                'initial_validity' => '18-11-2021 07:00:00',
                'final_validity' => '18-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => false,
                'website' => true,
                'contingency' => false
            ]
        ];

        $response = $this->json('POST', 'api/prefixes', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('prefixes', [
            'type' => Prefix::INVOICE,
            'company_id' =>  $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_store_prefix_contingency()
    {
        $this->initTestData();

        $data = [
            [
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::INVOICE,
                'prefix' => 'FE',
                'initial_validity' => '18-11-2021 07:00:00',
                'final_validity' => '18-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => true,
                'website' => true,
                'contingency' => true
            ],
            [
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::INVOICE,
                'prefix' => 'FA',
                'initial_validity' => '18-11-2021 07:00:00',
                'final_validity' => '18-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => false,
                'website' => false,
                'contingency' => true
            ],
            [
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::INVOICE,
                'prefix' => 'CEO',
                'initial_validity' => '18-11-2021 07:00:00',
                'final_validity' => '18-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => false,
                'website' => false,
                'contingency' => true
            ]
        ];

        $response = $this->json('POST', 'api/prefixes', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('prefixes', [
            'type' => Prefix::INVOICE,
            'company_id' =>  $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_store_prefix_supporting_document()
    {
        $this->initTestData();

        $data = [
            [
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::SUPPORTING_DOCUMENT,
                'prefix' => 'RELA',
                'initial_validity' => '10-11-2022 07:00:00',
                'final_validity' => '10-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => false,
                'website' => false,
                'contingency' => false,
                'supporting_document' => true
            ],
            [
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::SUPPORTING_DOCUMENT,
                'prefix' => 'GRS',
                'initial_validity' => '10-11-2022 07:00:00',
                'final_validity' => '10-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => false,
                'website' => false,
                'contingency' => false,
                'supporting_document' => true
            ],
            [
                'company_id' =>  $this->company->id,
                'resolution_number' => 00000,
                'type' => Prefix::SUPPORTING_DOCUMENT,
                'prefix' => 'PROS',
                'initial_validity' => '10-11-2022 07:00:00',
                'final_validity' => '10-11-2025 07:00:00',
                'final_authorization_range' => 000000,
                'initial_authorization_range' => 9500000,
                'physical_store' => false,
                'website' => false,
                'contingency' => false,
                'supporting_document' => true
            ],
        ];

        $response = $this->json('POST', 'api/prefixes', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('prefixes', [
            'type' => Prefix::SUPPORTING_DOCUMENT,
            'company_id' =>  $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_store_adjustment_notes()
    {
        $this->initTestData();

        $data = [
            [
                'company_id' =>  $this->company->id,
                'type' => Prefix::ADJUSTMENT_NOTE,
                'prefix' => 'RTS'
            ],
            [
                'company_id' =>  $this->company->id,
                'type' => Prefix::ADJUSTMENT_NOTE,
                'prefix' => 'HTR'
            ]
        ];

        $response = $this->json('POST', 'api/prefixes/notes', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('prefixes', [
            'company_id' =>  $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_store_prefix_notes()
    {
        $this->initTestData();

        $data = [
            [
                'company_id' =>  $this->company->id,
                'type' => Prefix::CREDIT_NOTE,
                'prefix' => 'FE'
            ],
            [
                'company_id' =>  $this->company->id,
                'type' => Prefix::DEBIT_NOTE,
                'prefix' => 'CEO'
            ]
        ];

        $response = $this->json('POST', 'api/prefixes/notes', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('prefixes', [
            'company_id' =>  $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_get_prefix_type()
    {
        $this->initTestData();
        Prefix::factory(3)->create(['company_id' => $this->company->id, 'contingency' => false]);

        $response = $this->post("api/prefixes/company/{$this->company->id}", Prefix::TYPE);
        $response->assertStatus(200);
        $response = $this->assertCount(3, $response->getOriginalContent()['data']);
    }

    /** @test */
    public function it_should_get_specific_prefix_type()
    {
        $this->initTestData();

        $prefix = Prefix::factory(5)->create(['type' => Prefix::INVOICE, 'company_id' => $this->company->id]);
        $data = [
            'company_id' => $this->company->id,
            'prefix_id' => $prefix[0]->id
        ];
        $response = $this->json('POST', 'api/prefixes/specific', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('prefixes', [
            'type' => Prefix::INVOICE,
            'company_id' =>  $this->company->id,
        ]);
    }

    /** @test */
    public function it_should_delete_prefixes_notes()
    {
        $this->initTestData();

        $invoiceService = new InvoiceService();
        $response = $invoiceService->getLastConsecutiveByPrefix($this->company->id)['data'];

        $prefix = Prefix::factory(5)->create([
            'type' => Prefix::DEBIT_NOTE,
            'company_id' => $this->company->id
        ])->pluck('id')->toArray();

        $this->assertCount(5, $prefix);
        $response = $this->json('DELETE', 'api/prefixes/delete', $prefix);
        $response->assertStatus(200);
        $this->assertDatabaseCount('prefixes', 0);
    }

    /** @test */
    public function it_should_get_synchronize_resolutions()
    {
        $this->initTestData();
        Prefix::factory()->create(['company_id' => $this->company->id, 'resolution_technical_key' => '5bf1c0b8169d854c346d27c5606cfa5c2c910da7c20d51c95be5d450cf7cf938']);
        $data = [
            'supplier_nit' => '901084754'
        ];
        $response = $this->json('POST', 'api/prefixes/synchronize', $data);
        // 3 prefixes synchronized from DIAN (Can be changed) and one created
        $this->assertCount(1, Prefix::all());
        $response->assertStatus(200);
    }

    /** @test */
    public function it_should_rank_depletion_resolution()
    {
        $this->initTestData();
        $prefix = Prefix::factory()->create([
            'company_id' => $this->company->id,
            'resolution_technical_key' => '5bf1c0b8169d854c346d27c5606cfa5c2c910da7c20d51c95be5d450cf7cf938',
            'final_authorization_range' => 99,
            'initial_authorization_range' => 00,
        ]);

        $data = [
            'prefix_id' => $prefix->id,
            'number' => 90
        ];

        $response = $this->json('POST', 'api/prefixes/rank-depletion/', $data);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_should_get_prefix_purchase()
    {
        $this->initTestData();
        $prefix = 'FC';
        $this->assertCount(0, Prefix::all());
        $data = ['prefix' => $prefix];
        $response = $this->post("api/prefixes/purchase/company", $data);
        $response->assertStatus(200);
        $this->assertCount(1, Prefix::all());
        $this->assertDatabaseHas('prefixes', [
            'prefix' => $prefix,
            'type' => Prefix::PURCHASE_SUPPLIER,
            'company_id' =>  $this->company->id,
        ]);

        $response = $this->post("api/prefixes/purchase/company", $data);
        $response->assertStatus(200);
        $this->assertCount(1, Prefix::all());
        $data = [];
        $response = $this->post("api/prefixes/purchase/company", $data);
        $response->assertStatus(200);
    }

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::first();
        $this->membership = Membership::factory()->create([
            'company_id' => $this->company->id
        ]);
    }

    /** @test */
    public function it_should_set_resolution_type(): void
    {
        $this->initTestData();
        $prefix = Prefix::factory(3)->create([
            'company_id' => $this->company->id,
            'resolution_technical_key' => '5bf1c0b8169d854c346d27c5606cfa5c2c910da7c20d51c95be5d450cf7cf938',
            'type' => Prefix::UNASSIGNED,
            'contingency' => false,
        ]);

        $this->assertDatabaseCount('prefixes', 3);

        $data = [
            [
                'type' => Prefix::INVOICE,
                'resolution_id' => $prefix[0]['id'],
                'contingency' => true,
            ],
            [
                'type' => Prefix::SUPPORTING_DOCUMENT,
                'resolution_id' => $prefix[1]['id'],
                'contingency' => false,
            ],
        ];
        $response = $this->post('api/prefixes/set-type', $data);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('prefixes', [
            'type' => Prefix::INVOICE,
            'company_id' => $this->company->id,
            'id' => $prefix[0]['id'],
            'contingency' => true,
        ]);
        $this->assertDatabaseHas('prefixes', [
            'type' => Prefix::SUPPORTING_DOCUMENT,
            'company_id' => $this->company->id,
            'id' => $prefix[1]['id'],
            'contingency' => false,
        ]);
        $this->assertDatabaseCount('prefixes', 3);
    }
}
