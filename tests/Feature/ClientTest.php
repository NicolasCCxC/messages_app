<?php

use App\Models\Company;
use App\Models\Membership;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientTest extends TestCase
{

    use RefreshDatabase;


    /**
     * @var Company
     */
    private $company;

    /**
     * add new client
     * expected ok response
     *
     * @test
     */
    public function it_should_add_new_client_with_company()
    {
        $company = Company::find(Company::factory()->create()->id);
        Client::factory()->create(['email' => 'test.5@gmail.com', 'name' => 'user']);
        $client = Client::find(Client::factory()->create()->id);
        $client->companies()->attach(['id'=>$company->id]);
        $this->actingAs($client);
        $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($client));
        $data = [
            'password' => 'testPassWord',
            'password_confirmation' => 'testPassWord',
            'email' => 'test.6@gmail.com',
            'company_id' => $company->id,
            'name' => 'testName',
        ];

        $response = $this->json('POST', '/client/clients', $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'email' => 'test.6@gmail.com',
            'name' => 'testName'
        ]);
        $this->assertDatabaseHas('clients', ["email" => "test.5@gmail.com"]);
    }


    /**
     * add new client with the same email but at the same company
     * expected ok response
     *
     * @test
     */
    public function it_should_add_new_client_with_company_and_email_registered_in_the_same_company()
    {
        $firstCompany = Company::find(Company::factory()->create()->id);

        $client = Client::factory()->create(['email' => 'test.5@gmail.com', 'name' => 'user']);
        $client = Client::find($client->id);

        $client->companies()->attach(['id'=>$firstCompany->id]);

        $this->actingAs($client);
        $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($client));

        $data = [
            'password' => 'testPassWord',
            'password_confirmation' => 'testPassWord',
            'email' => $client->email,
            'company_id' => $firstCompany->id,
            'name' => 'testName',
        ];

        $response = $this->json('POST', '/client/clients', $data);
        $response->assertStatus(Response::HTTP_CONFLICT);
    }


    /**
     * add new client with the same email
     * expected ok response
     *
     * @test
     */
    public function it_should_add_new_client_with_company_and_email_registered()
    {
        $firstCompany = Company::find(Company::factory()->create()->id);
        $secondCompany = Company::find(Company::factory()->create()->id);

        $client = Client::factory()->create(['email' => 'test.5@gmail.com', 'name' => 'user']);
        $client = Client::find($client->id);

        $client->companies()->attach(['id'=>$firstCompany->id]);

        $this->actingAs($client);
        $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($client));

        $data = [
            'password' => 'testPassWord',
            'password_confirmation' => 'testPassWord',
            'email' => $client->email,
            'company_id' => $secondCompany->id,
            'name' => 'testName',
        ];

        $response = $this->json('POST', '/client/clients', $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'email' => 'test.5@gmail.com',
            'name' => 'testName'
        ]);

        $this->assertDatabaseHas('clients', ["email" => "test.5@gmail.com"]);
    }

    /**
     * client creation through billing
     *
     * @test
     */
    public function it_should_add_new_client_from_billing(){
        $company = Company::find(Company::factory()->create()->id);
        $this->initTestData();
        $data = [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'document_type' => '0e2ea69e-7aaa-383f-9673-815d18dea696',
            'document_number' => '54684568',
            'company_id' => $company->id,
            'isPurchaseOrder' => false,
        ];
        $responseCreate = $this->json('POST', '/api/clients/billing', $data);
        $data = [
            'id' => $responseCreate->getOriginalContent()['data']['id'],
            'name' => 'testing',
            'email' => null,
            'document_type' => '0e2ea69e-7aaa-4857-9673-815d18dea696',
            'document_number' => '54684568',
            'company_id' => $company->id,
            'isPurchaseOrder' => true
        ];
        $response = $this->json('POST', '/api/clients/billing', $data);
        $response->assertStatus(Response::HTTP_OK);
        $dataEmailNull = [
            'name' => 'test',
            'email' => null,
            'document_type' => '0e2ea69e-7aaa-383f-9673-815d18dea696',
            'document_number' => '76767676',
            'company_id' => $company->id,
            'isPurchaseOrder' => false,
        ];
        $responseEmailNull = $this->json('POST', '/api/clients/billing', $dataEmailNull);
        $responseEmailNull->assertStatus(Response::HTTP_OK);
    }

    /**
     * add new client
     * expected bad response
     * because the data array is empty
     *
     * @test
     */
    public function it_should_failed_when_add_client()
    {
        $this->initTestData();
        $data = array();

        $response = $this->postJson('/client/clients', $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Update client
     *
     * @test
     */
    public function it_should_update_client()
    {
        $this->withoutExceptionHandling();
        $this->initTestData();
        $client = Client::factory()->create();
        $data = array(
            "id" => $client->id,
            'email' => 'test.5@gmail.com',
            'company_id' => $this->company->id,
            'name' => 'testName',
        );
        $client->type = "test type update";
        $response = $this->put("/client/clients", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Success operation',
            'data' => [
                [
                    'email' => 'test.5@gmail.com',
                    'companies' => [[
                        'id' => $this->company->id,
                    ]],
                    'name' => 'testName',
                ]
            ]
        ]);
        $this->assertDatabaseHas('clients', ["id" => $client->id]);
    }

    /**
     * Update client
     *
     * @test
     */
    public function it_try_update_client_when_this_not_exist_in_the_same_company()
    {
        $this->withoutExceptionHandling();
        $this->initTestData();
        $firstClient = Client::factory()->create(['email' => 'test.6@gmail.com', 'name' => 'user']);
        $secondClient = Client::factory()->create();
        $firstClient->companies()->attach(['id'=>$this->company->id]);
        $secondClient->companies()->attach(['id'=>$this->company->id]);

        $data = array(
            "id" => $firstClient->id,
            'email' => 'test.6@gmail.com',
            'company_id' => $this->company->id,
            'name' => 'testNam',
            'email_real'=> $secondClient->email,
        );

        $secondClient->type = "test type update";

        $response = $this->put("/client/clients", $data);
        $response->assertStatus(Response::HTTP_OK);

       $secondData = array(
           'id' => $secondClient->id,
           'email' => 'test.6@gmail.com',
           'company_id' => $this->company->id,
           'name' => 'testName',
       );

       $secondClient->type = "test type update";
       $response = $this->put("/client/clients", $secondData);
       $response->assertStatus(Response::HTTP_CONFLICT);
    }

    /**
     * Delete clients
     *
     * @test
     */
    public function it_should_delete_client()
    {
        $this->initTestData();
        $client = Client::factory(6)->create();
        $response = $this->delete("/client/clients/{$this->company->id}", $client->toArray());
        $response->assertStatus(Response::HTTP_OK);
        $this->assertJson('[]');
    }

    /**
     * Get a client by id
     *
     * @test
     */
    public function it_should_get_client_by_id()
    {
        $this->initTestData();
        $client = Client::factory()->create();
        $response = $this->get("client/clients/{$client->id}");
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function  should_get_the_last_login_date()
    {
        $this->initTestData();
        $company = Company::find(Company::factory()->create()->id);
        Client::factory()->create(['email' => 'test.5@gmail.com', 'name' => 'user']);
        $client = Client::find(Client::factory()->create()->id);
        $client->companies()->attach(['id'=>$company->id]);
        $this->actingAs($client);
        $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($client));
        $data = [
            'password' => 'testPassWord',
            'password_confirmation' => 'testPassWord',
            'email' => 'test.6@gmail.com',
            'company_id' => $company->id,
            'name' => 'testName',
            'last_login' => Carbon::now()->getTimestamp()
        ];
        
        $responseStore = $this->json('POST', '/client/clients', $data);
        $company = $responseStore->getOriginalContent()['data']['companies'][0]['id'];
        $clientId = $responseStore->getOriginalContent()['data']['id'];
        $data = [
            [
                'client_id' => $clientId,
                'company_id' => $company
            ]
        ];
        $response = $this->post('/api/clients/last-login', $data);
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_get_client_by_document_number () {
        $company = Company::find(Company::factory()->create()->id);
        $this->initTestData();
        $data = [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'document_type' => '0e2ea69e-7aaa-383f-9673-815d18dea696',
            'document_number' => '54684568',
            'company_id' => $company->id,
            'isPurchaseOrder' => false,
        ];
        $responseCreate = $this->json('POST', '/api/clients/billing', $data);

        $response = $this->get('api/clients/search/'.$responseCreate['data']['document_number'].'/'.$company->id);
        $response->assertStatus(Response::HTTP_OK);
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
