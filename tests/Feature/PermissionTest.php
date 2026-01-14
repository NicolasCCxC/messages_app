<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Membership;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Dotenv\Dotenv;

class PermissionTest extends TestCase
{

    use RefreshDatabase;


    /**
     * return all permission
     *
     * @test
     */
    public function it_should_get_all_permissions()
    {
        $this->initTestData();
        Permission::factory()->create();
        $response = $this->get('/api/permission');

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * store a new permission
     *
     * @test
     */
    public function it_should_store_permission()
    {
        $this->initTestData();
        $data = array(
            "name" => "permission name",
            "description" => "description"
        );
        $response = $this->post('/api/permission',$data);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * get all permission format
     *
     * @test
     */
    public function it_should_get_format_permissions()
    {
        $this->initTestData();

        $response = $this->get('/api/permission/format');

        $response->assertJsonCount(4);
        $response->assertJsonCount(11,'data');


        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * return all permission
     *
     * @test
     */
    public function it_should_get_update_permissions()
    {
        $this->initTestData();

        $this->assertDatabaseHas('permissions', [
            'name' => 'Armar catálogo de productos y/o servicios',
            'description' => Permission::SUBMODULE_PRODUCT_SERVICES
        ]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'Listado de productos/servicios agregados',
            'description' => Permission::SUBMODULE_PRODUCT_SERVICES
        ]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'Listado de productos/servicios editados',
            'description' => Permission::SUBMODULE_PRODUCT_SERVICES
        ]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'Listado de productos/servicios eliminados',
            'description' => Permission::SUBMODULE_PRODUCT_SERVICES
        ]);

        $this->assertDatabaseMissing('roles_permissions', [
            'permissions_id' => '7b2d49d0-421a-3c14-bafe-98b86ca30cf7'
        ]);

        $this->assertDatabaseMissing('roles_permissions', [
            'permissions_id' => '457527f4-3ccd-30ca-9124-97a7d4471778'
        ]);

        $this->assertDatabaseMissing('roles_permissions', [
            'permissions_id' => '1ba73ce5-2e25-33b0-8278-3646b5563f80'
        ]);
    }

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::factory()->create();
        $this->membership = Membership::factory()->create([
            'company_id' => $this->company->id
        ]);

    }


    public static function providerToken()
    {
        $dotenv = Dotenv::createImmutable('.', '.env.testing');
        $dotenv->load();
        return [
            'Random Token' => ['C9sb2NhbGhvc3RcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE2OTc4M', Response::HTTP_UNAUTHORIZED],
            'Login Token' => ['login', Response::HTTP_OK],
            'Service Token' => [env('SERVICE_TOKEN_TEST'), Response::HTTP_OK],
        ];
    }
    /**
     * get all permission format
     *
     * @test
     * @dataProvider providerToken
     */
    public function it_should_gateway_test(string $token, int $statusCode)
    {
        $this->initTestData();
        $userId = "b95f407b-c624-402a-af68-a2b86081d18e";
        $companyId = "83e80ae5-affc-32b4-b11d-b4cab371c48b";
        if ($token == "login")
        {
            $data = [
                'email' => 'fgonzalez@ccxc.us',
                'password' => '@!F+CCxC2@2@+E!@'
            ];
            
            $this->withHeader('Recaptcha', env('RECAPTCHA_TEST'));
            
            $response = $this->json('POST', '/api/auth/login', $data, [
                'Recaptcha' => env('RECAPTCHA_TEST'),
            ]);
            
            $token = $response->json()['data']['access_token'];
            $userId = $response->json()['data']['user']["id"];
            $companyId = $response->json()['data']['user']["company_id"];
        }
        
        $data = array(
            "resource"  => "/notifications/company/latest",
            "method" => "GET",
            "service" => "NOTIFICATION",
            "user_id" => $userId,
            "company_id" => $companyId
        );
        $header = [
            'Authorization' => 'Bearer '. $token
        ];
        $response = $this->post('/api/notification', $data, $header);

        $response->assertStatus($statusCode);
    }
}
