<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\EmailVerificationToken;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthClientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Company
     */
    private $company;

    /**
     * @test
     */
    public function it_should_store_verification_token()
    {
        $this->initTestData();

        $data = [
            'email' => 'emailprueba@gmail.com',
            'token' => 102032
        ];
        $response = $this->json('POST', '/client/auth-client/store-verification-token', $data);
        $response->assertStatus(Response::HTTP_CREATED);
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
    public function it_should_store_verification_token_with_malformed_email()
    {
        $this->initTestData();
        $data = [
            'email' => 'emailpruebagmail.com',
            'token' => 102032
        ];
        $response = $this->json('POST', '/client/auth-client/store-verification-token', $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_should_verify_token()
    {
        $company = Company::find(Company::factory()->create()->id);
        Client::factory()->create(['email' => 'test.5@gmail.com', 'name' => 'user']);
        $client = Client::find(Client::factory()->create()->id);
        $client->companies()->attach(['id' => $company->id]);
        $this->actingAs($client);
        $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($client));
        $companyId = '83e80ae5-affc-32b4-b11d-b4cab371c48b';

        $client = Client::factory()->create();
        DB::table('companies_clients')->insert([
            'company_id' => $companyId,
            'client_id' => $client->id
        ]);
        $emailVerificationToken = EmailVerificationToken::factory()->create([
            'email' => $client->email,
            'token' => Hash::make('123456')
        ]);
        $data = [
            'email' => $emailVerificationToken->email,
            'token' => 123456,
            'password' => '@!F+cCCC2@2@+E!@',
            'password_confirmation' => '@!F+cCCC2@2@+E!@',
            'company_id' => $companyId,
            "name" => "pruebas12"
        ];
        $response = $this->json('POST', '/client/auth-client/verify-token', $data);
        $response->assertStatus(Response::HTTP_OK);
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
    public function it_should_verify_token_with_expired_date()
    {
        $companyId = '83e80ae5-affc-32b4-b11d-b4cab371c48b';
        $client = Client::factory()->create();
        DB::table('companies_clients')->insert([
            'company_id' => $companyId,
            'client_id' => $client->id
        ]);
        $emailVerificationToken = EmailVerificationToken::factory()->create([
            'email' => $client->email,
            'token' => Hash::make('123456'),
            'created_at' => Carbon::now()->subDay()
        ]);
        $data = [
            'email' => $emailVerificationToken->email,
            'token' => 123456,
            'password' => '@!F+cCCC2@2@+E!@',
            'password_confirmation' => '@!F+cCCC2@2@+E!@',
            'company_id' => $companyId,
            "name" => "pruebas12"
        ];
        $response = $this->json('POST', '/client/auth-client/verify-token', $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'service',
            'errors'
        ]);
        $response->assertJsonFragment([
            "message" => "Token has expired"
        ]);
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
