<?php

namespace Tests\Feature;

use App\Infrastructure\Formulation\UserHelper;
use App\Models\Ciiu;
use App\Models\Client;
use App\Models\CompanyForeignExchange;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Models\SecurityFiscalResponsibility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var Membership
     */
    private $membership;

    /**
     * create a company with user without company
     *
     * @test
     */
    public function it_should_store_company()
    {
        $user = User::factory()->create([
            'company_id' => null
        ]);

        $this->actingAs($user);
        $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($user));

        $company = [
            'name' => 'TestCompany',
            'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
            'foreign_exchange_code' => CompanyForeignExchange::COP,
            'country_id' => 2,
            'country_name' => 'Colombia',
            'department_id' => 56,
            'department_name' => 'Tolima',
            'city_id' => 67,
            'city_name' => 'Natagaima',
            'address' => 'Crr 10 # 12 - 14',
            'company_representative_name' => 'Test User',
            'document_type' =>  '4c74deca-d528-11eb-b8bc-0242ac130003',
            'document_number' => '1025365452',
            'postal_code' => '1111111',
            'phone' => '3259658523',
            'domain' => 'app.test.com',
            'email' => 'test@test.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'accept_company_privacy' => true,
            'has_a_physical_store' => true,
            'has_e_commerce' => true,
            'company_privacy_acceptation_date' => 1629908978,
            'whatsapp' => '3109658523',
            'tax_detail' => '1',
            'ciius' => [
                [
                    'code' => 6432,
                    'name' => 'code 1',
                    'ciiu_id' => 1,
                ],
                [
                    'code' => 6431,
                    'name' => 'code 2',
                    'ciiu_id' => 1,
                ],
                [
                    'code' => 6422,
                    'name' => 'code 3',
                    'ciiu_id' => 1,
                ],
            ],
            'companies_foreign_exchange' => [
                [
                    'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
                    'is_active' => true,
                ]
            ],
            'fiscal_responsibilities' => [
                [
                    'id' => '1',
                    'withholdings' => [
                        [
                            'is_active' => false,
                            'name' => 'RETEIVA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEICA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEFUENTE'
                        ]
                    ]
                ],
                [
                    'id' => '2',
                    'withholdings' => [
                        [
                            'is_active' => true,
                            'name' => 'RETEIVA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEICA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEFUENTE'
                        ]
                    ]
                ],
            ],
        ];

        $response = $this->json('POST', 'api/company', $company);
        $this->assertCount(1, CompanyForeignExchange::all());
        $this->assertCount(3, SecurityFiscalResponsibility::all());
        $this->assertCount(0, Client::all());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => $company['name'],
            'tax_detail' => $company['tax_detail']
        ]);

        $this->assertDatabaseCount('ciius_company', 3);

        $company = [
            'name' => 'test company update',
            'document_type' => '4c74deca-d528-11eb-b8bc-0242ac130003',
            'document_number' => "{rand(5, 10)}",
            'company_representative_name' => 'Test User',
            'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
            'foreign_exchange_code' => CompanyForeignExchange::COP,
            'country_id' => 2,
            'country_name' => 'Colombia',
            'department_id' => 56,
            'department_name' => 'Tolima',
            'city_id' => 67,
            'city_name' => 'Natagaima',
            'postal_code' => '1111111',
            'address' => 'Crr 10 # 12 - 14',
            'phone' => '3259658523',
            'domain' => 'app.test.com',
            'email' => 'test@test.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'accept_company_privacy' => true,
            'company_privacy_acceptation_date' => 1629908978,
            'whatsapp' => '3109658523',
            'tax_detail' => '1',
            'ciius' => [
                [
                    'code' => 6422,
                    'name' => 'code 3',
                    'ciiu_id' => 1,
                ],
            ]
        ];

        $response = $this->json('POST', 'api/company', $company);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => $company['name'],
        ]);

        $this->assertDatabaseHas('ciius_company', [
            'name' => $company['ciius'][0]['name'],
        ]);

        $this->assertDatabaseCount('ciius_company', 1);
    }


    /**
     * @test
     */
    public function it_should_get_company_details()
    {
        $this->initTestData();
        $response = $this->json('GET', "/api/company/{$this->company->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
    }

    /**
     * @test
     */
    public function it_should_get_company_with_email_details()
    {
        $this->initTestData();
        $company = [
            'name' => 'test company update',
            'document_type' => '4c74deca-d528-11eb-b8bc-0242ac130003',
            'document_number' => '1025365452',
            'company_representative_name' => 'Test User',
            'country_id' => 2,
            'country_name' => 'Colombia',
            'department_id' => 56,
            'department_name' => 'Tolima',
            'city_id' => 67,
            'city_name' => 'Natagaima',
            'postal_code' => '1111111',
            'address' => 'Crr 10 # 12 - 14',
            'phone' => '3259658523',
            'domain' => 'app.test.com',
            'email' => 'test@test.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'accept_company_privacy' => true,
            'company_privacy_acceptation_date' => 1629908978,
            'whatsapp' => '3109658523',
            'tax_detail' => '1',
            'ciius' => [
                [
                    'code' => 3,
                    'name' => 'code 3',
                    'ciiu_id' => 1,
                ],
            ]
        ];

        $response = $this->json('POST', 'api/company', $company);
        $response->assertStatus(200);
        $companyId = $response->getOriginalContent()['data']['company']['id'];
        $response = $this->json('GET', "/api/company/email");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'email'
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_should_update_company()
    {
        $this->initTestData();
        $updatedCompany = [
            'id' => $this->company->id,
            'name' => 'CCxC',
            'document_type' => 'fc2a647b-c8b4-313f-b799-d5f883ff55e1',
            'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
            'foreign_exchange_code' => CompanyForeignExchange::COP,
            'document_type_name' => 'RUT',
            'document_number' => '9010847543',
            'company_representative_name' => "Laura Alvarez",
            'ciiu_id' => 1,
            'ciiu_code' => '6358',
            'phone' => '7943044',
            'country_id' => 2,
            'country_name' => 'Colombia',
            'department_id' => 56,
            'department_name' => 'Tolima',
            'city_id' => 67,
            'city_name' => 'Natagaima',
            'postal_code' => '111111',
            'address' => 'Cra 13 # 94a-26 Of. 301',
            'company_privacy_acceptation_date' => 1629244800,
            'created_at' => false,
            'domain' => 'app-famiefi-ccxc.co',
            'make_web_page_type' => 'LEGAL_PERSON',
            'updated_at' => false,
            'brand_established_service' => false,
            'accept_company_privacy' => true,
            'has_a_physical_store' => true,
            'has_e_commerce' => true,
            'whatsapp' => '3109658523',
            'memberships' => []
        ];

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => $this->company->name,
        ]);

        $response = $this->json('PUT', "api/company/company/{$this->company->id}", $updatedCompany);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => $updatedCompany['name'],
        ]);
    }

    /** @test */
    public function it_should_get_staff_company()
    {
        $this->initTestData();
        $response = $this->json('GET', 'api/company/company-staff');
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'users',
                'clients'
            ]
        ]);
    }

    /** @test */
    public function it_should_crud_rut()
    {
        $this->it_should_crud_company_attachment('RUT');
    }

    /** @test */
    public function it_should_crud_logo()
    {
        $this->it_should_crud_company_attachment('logo');
    }

    /**
     *  General test of the attachment CRUD
     *
     * @param $file
     */
    public function it_should_crud_company_attachment($file)
    {
        $this->initTestData();
        $data = [
            'file' => UploadedFile::fake()->image('avatar.png', 1024),
            'company_id' => $this->company->id,
            'service' => 'BUCKET',
            'folder' => $file,
            'data' => '{"uuid_company": "8ef71c88-44d6-3fee-b227-e9f62bef6517"}',
            'method' => 'POST',
            'user-id' => '56eb3343-7d2f-4a09-abba-f8dcc73b44e6',
            'resource' => '/bucket/upload-file'
        ];

        // Load a new company attachment
        $createResponse = $this->json('POST', 'api/company/upload-company-attachment', $data);

        $createResponse->assertStatus(200);
        $this->assertDatabaseCount('attachments', 1);

        //Get company attachment
        $getResponse = $this->get('api/company/upload-company-attachment/' . $this->company->id . '/' . $data['folder']);
        $getResponse->assertStatus(200);
        $getResponse->assertJsonStructure([
            'data' => [
                'id',
                'bucket' => [
                    'company_id',
                ],
                'url'
            ]
        ]);

        // Update company attachment
        $bucketId = $createResponse->getOriginalContent()['data']['bucket_id'];
        $dataUpdate = [
            'file' => UploadedFile::fake()->image('updateFile.png', 1024),
            'company_id' => $this->company->id,
            'service' => 'BUCKET',
            'folder' => $file,
            'data' => '{"uuid_company": "8ef71c88-44d6-3fee-b227-e9f62bef6517"}',
            'method' => 'POST',
            'user-id' => '56eb3343-7d2f-4a09-abba-f8dcc73b44e6',
            'bucket_detail_id' => $bucketId,
            'resource' => '/bucket/upload-file/update'
        ];

        $updateResponse = $this->json('POST', 'api/company/upload-company-attachment/', $dataUpdate);
        $updateResponse->assertStatus(200);
        $this->assertDatabaseCount('attachments', 1);
        $this->assertDatabaseHas('attachments', [
            'bucket_id' => $bucketId
        ]);

        // Delete company attachment
        $deleteResponse = $this->json('DELETE', 'api/company/upload-company-attachment/' . $this->company->id . '/' . $data['folder']);
        $deleteResponse->assertStatus(200);
    }

    /** @test */
    /* public function should_test_update_attachment_logo_by_id()
    {
        $this->initTestData();
        $data = [
            'file' => UploadedFile::fake()->image('avatar.png', 1024),
            'company_id' => $this->company->id,
            'service' => 'BUCKET',
            'folder' => 'logo',
            'data' => '{"uuid_company": "8ef71c88-44d6-3fee-b227-e9f62bef6517"}',
            'method' => 'POST',
            'user-id' => '56eb3343-7d2f-4a09-abba-f8dcc73b44e6',
            'resource' => '/bucket/upload-file'
        ];

        // Load a new company attachment
        $createResponse = $this->json('POST', 'api/company/upload-company-attachment', $data);
        $createResponse->assertStatus(200);
        $this->assertDatabaseCount('attachments', 1);


        //Get company attachment
        $getResponse = $this->get('api/company/upload-company-attachment/' . $this->company->id . '/' . $data['folder']);
        $getResponse->assertStatus(200);
        $getResponse->assertJsonStructure([
            'data' => [
                'id',
                'bucket' => [
                    'company_id',
                ],
                'url'
            ]
        ]);

        $data = [
            'company_id' => $this->company->id,
            'service' => 'BUCKET',
            'folder' => 'logo',
            'data' => '{"uuid_company": "8ef71c88-44d6-3fee-b227-e9f62bef6517"}',
            'method' => 'POST',
            'user-id' => '56eb3343-7d2f-4a09-abba-f8dcc73b44e6',
            'resource' => '/bucket/upload-file',
            'is_bucket_detail_id' => true,
            'bucket_data' => '{"message":"Success operation","statusCode":200,"service":"BUCKET","data":{"id":"2912c34b-d26a-4ca7-8869-cee7c1e6de4c","bucket":{"id":"2912c34b-d26a-4ca7-8869-cee7c1e6de4c","company_id":"9abf550e-499e-3fc9-89b7-d3e035f926fc","service":"BUCKET","url":"https://storageccxc1.s3.us-west-2.amazonaws.com/famiefi/469831bb-b5d0-31f6-88b0-332aedc1371f/inventory/products/779c54dc-ba22-3b05-977f-470c0c0dee95/2022-08-05-28635656-3b23-380a-bc1c-f724c6f02e5c-1659719823.png"},"file_type":"image\/png","file_name":"2022-04-28-68334953-cf3b-3967-b83b-69a2aaf490ed-1651167298.png","file_original_name":"avatar.png","url":"https://storageccxc1.s3.us-west-2.amazonaws.com/famiefi/469831bb-b5d0-31f6-88b0-332aedc1371f/inventory/products/779c54dc-ba22-3b05-977f-470c0c0dee95/2022-08-05-28635656-3b23-380a-bc1c-f724c6f02e5c-1659719823.png"}}',
        ];

        $createResponse = $this->json('POST', 'api/company/upload-company-attachment', $data);

        $createResponse->assertStatus(200);
        $this->assertDatabaseCount('attachments', 1);
        $this->assertDatabaseHas('attachments', [
            'bucket_id' => '2912c34b-d26a-4ca7-8869-cee7c1e6de4c'
        ]);

    } */

    /** @test */
    public function it_should_update_domain()
    {
        $this->initTestData();

        $data = [
            'company_id' => $this->company->id,
            'domain' => $newDomain = 'www.test-famiefi.com'
        ];

        $this->post('api/company/update-domain', $data);

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'domain' => $newDomain
        ]);
    }

    /** @test */
    public function it_should_get_invoices_available_by_companyId()
    {
        $user = User::where('company_id', "83e80ae5-affc-32b4-b11d-b4cab371c48b")->first();
        $this->signIn($user);
        $company = Company::find(Company::COMPANY_CCXC);
        $company->created_at = Carbon::now()->subDay(1);
        $company->save();
        $response = $this->json('GET', "api/invoices-available/83e80ae5-affc-32b4-b11d-b4cab371c48b");
        $response->assertStatus(Response::HTTP_OK);

        $membership = Membership::factory()->create([
            'company_id' => Company::COMPANY_CCXC,
            'is_active' => true,
            'payment_method' => 'PAYU',
            'payment_status' => 'APPROVED',
        ]);

        $membershipHasModule = MembershipHasModules::factory()->create([
            'membership_id' => $membership->id,
            'membership_modules_id' => 3,
            'is_active' => true,
        ]);

        MembershipSubModule::factory()->create([
            'membership_has_modules_id' => $membershipHasModule->id,
            'sub_module_id' => 4,
            'is_active' => true,
        ]);

        $company->invoices_available = 720;
        $company->save();

        $response = $this->json('GET', "api/invoices-available/83e80ae5-affc-32b4-b11d-b4cab371c48b");
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_get_supporting_document_by_companyId()
    {
        $user = User::where('company_id', "83e80ae5-affc-32b4-b11d-b4cab371c48b")->first();
        $this->signIn($user);
        $company = Company::find(Company::COMPANY_CCXC);
        $company->created_at = Carbon::now()->subDay(1);
        $company->save();
        $response = $this->json('GET', "api/supporting-document-available/83e80ae5-affc-32b4-b11d-b4cab371c48b");
        $response->assertStatus(Response::HTTP_OK);

        $membership = Membership::factory()->create([
            'company_id' => Company::COMPANY_CCXC,
            'is_active' => true,
            'payment_method' => 'PAYU',
            'payment_status' => 'APPROVED',
        ]);

        $membershipHasModule = MembershipHasModules::factory()->create([
            'membership_id' => $membership->id,
            'membership_modules_id' => 3,
            'is_active' => true,
        ]);

        MembershipSubModule::factory()->create([
            'membership_has_modules_id' => $membershipHasModule->id,
            'sub_module_id' => 4,
            'is_active' => true,
        ]);

        $company->invoices_available = 720;
        $company->save();

        $response = $this->json('GET', "api/supporting-document-available/83e80ae5-affc-32b4-b11d-b4cab371c48b");
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_get_active_memberships_by_company()
    {
        $this->initTestData();
        $response = $this->json('GET', "api/company/active-memberships/{$this->company->id}");
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_update_information_billing()
    {
        $this->initTestData();
        $company = Company::factory()->create(['foreign_exchange_id' => null, 'foreign_exchange_code' => null, 'name' => 'Prueba', 'person_type' => null, 'tax_detail' => null]);
        $user = User::factory()->create(['company_id' => $company->id]);
        UserHelper::assignSuperAdminRole($user->id);
        $data = [
            'fiscal_responsibilities' => [
                [
                    'id' => '1',
                    'withholdings' => [
                        [
                            'is_active' => false,
                            'name' => 'RETEIVA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEICA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEFUENTE'
                        ]
                    ]
                ],
                [
                    'id' => '2',
                    'withholdings' => [
                        [
                            'is_active' => true,
                            'name' => 'RETEIVA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEICA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEFUENTE'
                        ]
                    ]
                ],

            ],
            'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
            'foreign_exchange_code' => CompanyForeignExchange::COP,
            'person_type' => 'LEGAL_PERSON',
            'person_type_name' => 'Persona Jurídica',
            'tax_detail' => '1',
            'companies_foreign_exchange' => [
                [
                    'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
                    'is_active' => true,
                ]
            ]
        ];
        $response = $this->put("api/company/update-billing", $data);
        $this->assertCount(1, CompanyForeignExchange::all());
        $response->assertStatus(Response::HTTP_OK);
        $dataUpdate = [
            'fiscal_responsibilities' => [
                [
                    'id' => '1',
                    'withholdings' => [
                        [
                            'is_active' => false,
                            'name' => 'RETEIVA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEICA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEFUENTE'
                        ]
                    ]
                ],
                [
                    'id' => '2',
                    'withholdings' => [
                        [
                            'is_active' => true,
                            'name' => 'RETEIVA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEICA'
                        ],
                        [
                            'is_active' => false,
                            'name' => 'RETEFUENTE'
                        ]
                    ]
                ],

            ],
            'foreign_exchange_id' =>  CompanyForeignExchange::IDS[CompanyForeignExchange::COP],
            'foreign_exchange_code' => CompanyForeignExchange::COP,
            'person_type' => 'LEGAL_PERSON',
            'person_type_name' => 'Persona Jurídica',
            'tax_detail' => '1',
            'companies_foreign_exchange' => [
                [
                    'id' => CompanyForeignExchange::all()[0]['id'],
                    'foreign_exchange_id' => CompanyForeignExchange::IDS[CompanyForeignExchange::AFN],
                    'is_active' => true,
                ]
            ]
        ];
        $responseUpdate = $this->put("api/company/update-billing", $dataUpdate);
        $this->assertCount(1, CompanyForeignExchange::all());
        $responseUpdate->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_get_company_domain_by_id()
    {
        $this->initTestData();
        $response = $this->json('GET', "api/company/get-domain/{$this->company->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            "message" => "Success operation",
            "statusCode" => 200,
            "service" => "SECURITY",
            "data" => $this->company->domain
        ]);
    }

    /** @test */
    public function it_should_get_names_companies()
    {
        $this->initTestData();
        $companyOne = Company::factory()->create(['name' => 'Prueba']);
        $companyTwo = Company::factory()->create(['name' => 'Prueba2']);
        $companyThree = Company::factory()->create(['name' => 'Prueba3']);

        $response = $this->json('POST', 'api/company/get-names-companies', [$companyOne->id, $companyTwo->id]);
        $response->assertStatus(Response::HTTP_OK);
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
    public function it_should_get_client_companies_logo()
    {
        $this->initTestData();
        $company = Company::factory()->create(['name' => 'Prueba']);


        $data = [
            "domain" => $company->domain
        ];

        $response = $this->json('POST', 'api/company/company-logo', $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'url',
                'company_name'
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_should_update_company_minimun_data()
    {
        $this->initTestData();
        $updatedCompany = [
            'name' => 'CCxC',
            'document_type' => 'fc2a647b-c8b4-313f-b799-d5f883ff55e1',
            'document_number' => '9010847543',
            'person_type' => 'LEGAL_PERSON'
        ];

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => $this->company->name,
        ]);

        $response = $this->json('PUT', "api/company/update-company-minimum-data/{$this->company->id}", $updatedCompany);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => $updatedCompany['name'],
        ]);
    }

    /**
     * @test
     */
    public function it_should_get_all_companies()
    {
        $this->initTestData();
        $response = $this->json('GET', "api/company/all-companies");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data'
        ]);
    }

    /** @test */
    public function it_should_get_information_billing()
    {
        $this->initTestData();

        $response = $this->json('GET', "api/company/information-by-billing/{$this->company->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'data' => [
                'email',
                'name',
                'person_type',
                'attachments',
                'fiscal_responsibilities',
                'params_from_utils',
                'prefixes',
                'company_taxes',
                'document_type_name'
            ]
        ]);
    }


    private function initTestData()
    {
        $this->company = Company::factory()->create(['name' => 'Prueba']);
        Ciiu::factory()->create(['company_id' => $this->company->id]);
        $user = User::factory()->create(['company_id' => $this->company->id]);
        UserHelper::assignSuperAdminRole($user->id);
        $this->signIn($user);

        $membership = Membership::factory()->create([
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $membershipHasModule = MembershipHasModules::factory()->create([
            'membership_id' => $membership->id,
            'membership_modules_id' => 3,
            'is_active' => true,
        ]);

        MembershipSubModule::factory()->create([
            'membership_has_modules_id' => $membershipHasModule->id,
            'sub_module_id' => 4,
            'is_active' => true,
        ]);
    }
}
