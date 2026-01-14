<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\PhysicalStore;
use App\Models\PointSale;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class PhysicalStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var PhysicalStore
     */
    private $PhysicalStore;

    /**
     * @var PointSale
     */
    private $pointSale;

    /**
     * @test
     */
    public function get_physical_stores_by_company()
    {
        $this->initTestData();
        $user = User::factory()->create(['company_id' => $this->company->id]);
        $this->signIn($user);
        $response = $this->json('GET', "/api/company/physical-store");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                [
                    'id',
                    'company_id',
                    'name',
                    'address',
                    'point_sales' => [
                        [
                            'id',
                            'physical_store_id',
                            'name',
                            'contact_link'
                        ]
                    ],
                    'country_id',
                    'country_name',
                    'department_id',
                    'department_name',
                    'city_id',
                    'city_name',
                    'phone'
                ]
            ]
        ]);
    }



    /**
     * @test
     */
    public function should_create_physical_store()
    {
        $this->initTestData();
        $this->assertCount(1, PhysicalStore::all());
        $this->assertCount(1, PointSale::all());
        $request = [
            [
                'id' => $this->PhysicalStore->id,
                'company_id' => $this->company->id,
                'name' => 'test',
                'address' => 'avenida siempre viva 1',
                'country_id' => 2,
                'country_name' => 'Colombia',
                'department_id' => 56,
                'department_name' => 'Tolima',
                'city_id' => 67,
                'city_name' => 'Natagaima',
                'phone' => '3259658523',
                'point_sales' => [
                    [
                        'id' => $this->pointSale->id,
                        'name' => 'multipuntos',
                        'contact_link' => ''
                    ],
                    [
                        'name' => 'multipuntos 2',
                        'contact_link' => 'link 2'
                    ]
                ]
            ],
            [
                'company_id' => $this->company->id,
                'name' => 'test',
                'address' => 'avenida siempre viva 2',
                'country_id' => 2,
                'country_name' => 'Colombia',
                'department_id' => 56,
                'department_name' => 'Tolima',
                'city_id' => 67,
                'city_name' => 'Natagaima',
                'phone' => '3259658523',
                'point_sales' => [
                    [
                        'name' => 'redeban'
                    ]
                ]
            ]
        ];
        $response = $this->json('POST', 'api/company/physical-store', $request);
        $response->assertStatus(200);
        $this->assertCount(2, PhysicalStore::all());
        $this->assertCount(3, PointSale::all());

        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                [
                    'id',
                    'company_id',
                    'name',
                    'address',
                    'point_sales' => [
                        [
                            'id',
                            'physical_store_id',
                            'name',
                            'contact_link'
                        ]
                    ],
                    'country_id',
                    'country_name',
                    'department_id',
                    'department_name',
                    'city_id',
                    'city_name',
                    'phone'
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function deleted_physical_stores_by_id()
    {
        $this->initTestData();
        $this->assertCount(1, PhysicalStore::all());
        $response = $this->json('DELETE', "/api/company/physical-store/{$this->PhysicalStore->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'status'
            ]
        ]);
        $this->assertCount(0, PhysicalStore::all());
    }

    /**
     * @test
     */
    public function deleted_point_sale_physical_stores_by_id()
    {
        $this->initTestData();
        $this->assertCount(1, PointSale::all());
        $response = $this->json('DELETE', "/api/company/physical-store/point-sale/{$this->pointSale->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'status'
            ]
        ]);
        $this->assertCount(0, PointSale::all());
    }

    /**
     * @test
     */
    public function deleted_point_sale_or_physical_stores_by_ids()
    {
        $this->initTestData();
        $this->assertCount(1, PointSale::all());
        $this->assertCount(1, PhysicalStore::all());
        $data = [
            $this->pointSale->id,
            $this->PhysicalStore->id
        ];
        $response = $this->json('DELETE', "/api/company/physical-store/physicals-or-points",$data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'physical_store',
                'point_sales'
            ]
        ]);
        $this->assertCount(0, PointSale::all());
        $this->assertCount(0, PhysicalStore::all());
    }

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::factory()->create();
        $this->PhysicalStore = PhysicalStore::factory()->create([
            'company_id' => $this->company->id
        ]);

        $this->pointSale = PointSale::factory()->create([
            'physical_store_id' => $this->PhysicalStore->id
        ]);
    }
}
