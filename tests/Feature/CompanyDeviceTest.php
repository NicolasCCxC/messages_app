<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyDevice;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\User;

class CompanyDeviceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Company
     */
    private $company;

    /** @test */
    public function it_should_create_company_devices()
    {
        $this->initTestData();
        $user = User::factory()->create(['company_id' => $this->company->id]);
        $this->signIn($user);
        $data = [
            'company_id' => $this->company->id,
            'devices' => [
                [
                    'name' => 'usb'
                ]
            ]
        ];
        $response = $this->json('POST', '/api/companies-devices', $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, CompanyDevice::all());

        $response = $this->json('POST', '/api/companies-devices', $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, CompanyDevice::all());
        $dataUpdate = [
            'company_id' => $this->company->id,
            'devices' => [
                [
                    'id' => CompanyDevice::first()->id,
                    'name' => 'test dispositivo'
                ]
            ]
        ];
        $response =$this->json('POST', '/api/companies-devices', $dataUpdate);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, CompanyDevice::all());

        $response =$this->json('GET', '/api/companies-devices/company/'.$this->company->id, $dataUpdate);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [[
              'id',
              'name'
            ]]
        ]);
    }

    /** @test */
    public function it_should_delete_company_devices()
    {
        $this->initTestData();
        $companyDevice = CompanyDevice::factory(3)->create(['company_id' => $this->company->id]);
        $data = [
            'company_id' => $this->company->id,
            'ids' => [
                $companyDevice[0]->id,
                $companyDevice[1]->id
            ]
        ];
        $response = $this->json('DELETE', '/api/companies-devices/many', $data);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, CompanyDevice::all());
    }

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::factory()->create();
        Membership::factory()->create([
            'company_id' => $this->company->id
        ]);

    }
}
