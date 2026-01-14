<?php

use App\Infrastructure\Formulation\UserHelper;
use App\Models\Company;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserTest extends TestCase
{

    use RefreshDatabase;


    /**
     * @var Company
     */
    private $company;

  /**
   * add new user
   * expected ok response
   *
   * @test
   */
  public function it_should_add_new_user_with_company()
  {
    $company = Company::factory()->create();
    $user = User::factory()->create([
        'company_id' => $company->id
    ]);
    $this->actingAs($user);
    $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($user));
    $this->withHeader('Recaptcha', env('RECAPTCHA_TEST'));
    $data = [
      'password' => 'Admin.2020!',
      'password_confirmation' => 'Admin.2020!',
      'email' => 'test.5@gmail.com',
      'document_type' => "f73f5793-795e-33db-9115-95437f9ecaea",
      'company_id' => $company->id,
      'name' => 'testName',
      'accept_policy' => false,
      'accept_terms' => false,
      'roles' =>
      [
        [
          'name' => 'Administrador2',
          'permissions' =>
          [
            [
              'name' => 'lalalalalalala',
              'description' => 'jsakgsdsdahsdahg',
            ],
          ],
        ],
        [
          'name' => 'Página web2',
          'permissions' =>
          [
            [
              'name' => 'tataala',
              'description' => 'jsakgscxasdsdahsdahg',
            ],
          ],
        ],
      ],
    ];

    $response = $this->json('POST','/api/users', $data,[
        'Recaptcha' => env('RECAPTCHA_TEST'),
    ]);
    $response->assertStatus(Response::HTTP_CREATED);
    $this->assertCount(38, User::all());
    $response->assertJsonFragment([
          'email' => 'test.5@gmail.com',
          'name' => 'testName',
    ]);
    $response->assertJsonFragment([
                'name' => 'Administrador2',
    ]);
    $response->assertJsonFragment([
        'name' => 'Página web2',
    ]);
    $this->assertDatabaseHas('users', ["email" => "test.5@gmail.com", "document_type" => "f73f5793-795e-33db-9115-95437f9ecaea"]);
  }

    /**
     * add new user
     * expected bad response
     * because the data array is empty
     *
     * @test
     */
    public function it_should_failed_when_add_user()
    {
        $this->initTestData();
        $data = array();

        $response = $this->postJson('/api/users', $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Update user
     *
     * @test
     */
    public function it_should_update_user()
    {
        $this->initTestData();
        $user = User::factory()->create();
        $roles = array(
            0  =>
                array(
                    'name' => 'Página web1',
                    'permissions' =>
                        array(
                            0 =>
                                array(
                                    'name' => 'tataala',
                                    'description' => 'jsakgscxasdsdahsdahg',
                                ),
                        ),
                ),
        );

        UserHelper::assignRolesAndPermissions($user, $roles);

        $newRole1 = "Administrador2";
        $newRole2 = "Página web2";

        $data = array(
            "id" => $user->id,
            'email' => 'test.5@gmail.com',
            'company_id' => $this->company->id,
            'name' => 'testName',
            'type' => 'test type update',
            'roles' =>
                array(
                    0 =>
                        array(
                            'name' => $newRole1,
                            'permissions' =>
                                array(
                                    0 =>
                                        array(
                                            'name' => 'lalalalalalala',
                                            'description' => 'jsakgsdsdahsdahg',
                                        ),
                                ),
                        ),
                    1 =>
                        array(
                            'name' => $newRole2,
                            'permissions' =>
                                array(
                                    0 =>
                                        array(
                                            'name' => 'tataala',
                                            'description' => 'jsakgscxasdsdahsdahg',
                                        ),
                                ),
                        ),
                ),
        );

        $this->assertDatabaseMissing('roles', ["name" => $newRole1]);
        $this->assertDatabaseMissing('roles', ["name" => $newRole2]);

        $user->type = "test type update";
        $response = $this->put("/api/users", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Success operation',
            'data' => []
        ]);

        $stringResponse = json_encode(collect(json_decode($response->getContent())->data)->where('id',$user->id)->first()->roles);
        $this->assertStringContainsString(json_encode($newRole1), $stringResponse);
        $this->assertStringContainsString(json_encode($newRole2), $stringResponse);

        $this->assertDatabaseHas('roles', ["name" => $newRole1]);
        $this->assertDatabaseHas('roles', ["name" => $newRole2]);

        $this->assertDatabaseHas('users', ["id" => $user->id, 'type' => 'test type update']);
    }

    /**
     * Delete users
     *
     * @test
     */
    public function it_should_delete_user()
    {
        $this->initTestData();
        $user = User::factory(6)->create(['company_id' => $this->company->id]);
        $this->signIn($user[0]);
        $response = $this->delete("/api/users/", $user->toArray());
        $response->assertStatus(Response::HTTP_OK);
        $this->assertJson('[]');
    }

    /**
     * Filter by user permission
     *
     * @test
     */
    public function it_should_filter_by_user_permission()
    {
        $this->initTestData();
        $response = $this->get("api/users/role/83e80ae5-affc-32b4-b11d-b4cab371c48b");
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Get a user by id
     *
     * @test
     */
    public function it_should_get_user_by_id()
    {
        $this->initTestData();
        $user = User::factory()->create();
        $response = $this->get("api/users/user/{$user->id}");
        $response->assertStatus(Response::HTTP_OK);
    }

  /**
   * Get a Super user by company id
   *
   * @test
   */
  public function it_should_get_super_user_by_company()
  {
    $this->initTestData();
    $user = User::factory()->create();
    $response = $this->get("api/users/super/{$user->company_id}");
    $response->assertStatus(Response::HTTP_OK);

    $response->assertJsonStructure([
      'message',
      'statusCode',
      'service',
      'data' => [
        'email'
      ]
    ]);
  }

    private function initTestData()
    {
        $this->signIn();
        $this->company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $this->company->id]);
        UserHelper::assignSuperAdminRole($user->id);
        $this->membership = Membership::factory()->create([
            'company_id' => $this->company->id
        ]);


    }

    /** @test */
    public function it_should_get_information_after_login_and_update_first_login_user()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $this->withHeader('Recaptcha', env('RECAPTCHA_TEST'));

        $response = $this->json('POST', '/api/auth/login', $data, [
            'Recaptcha' => env('RECAPTCHA_TEST'),
        ]);
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', ["id" => $user->id, 'is_first_login' => true]);
        $this->signIn($user);
        $response = $this->json('PUT', "/api/auth/user/update-first-login/{$user->id}");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('users', ["id" => $user->id, 'is_first_login' => false]);

    }

    /** @test */
    public function get_available_users()
    {
        $this->initTestData();
        $user = User::factory()->create(['company_id' => $this->company->id]);
        $this->signIn($user);
        $response = $this->get("api/users/available");
        $response->assertStatus(Response::HTTP_OK);
        $quantity_users = $response->json()['data'];
        $company = Company::find($this->company->id);
        $numberOfUsers = User::where('company_id', $this->company->id)->count();
        $this->assertEquals(($company->users_available - $numberOfUsers + 1), $quantity_users);
    }

    /** @test */
    public function get_jwt_company_service()
    {
        $this->initTestData();
        $user = User::factory()->create(['company_id' => $this->company->id]);
        $this->signIn($user);
        $data = ['service' => "INVENTORY"];
        $response = $this->json('POST', '/api/auth/add-company-jwt-services', $data);
        $response->assertStatus(Response::HTTP_OK);
    }
}
