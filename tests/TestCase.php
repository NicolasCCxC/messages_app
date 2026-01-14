<?php

namespace Tests;

use App\Models\User;
use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\SQLiteBuilder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Fluent;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->hotfixSqlite();
        parent::__construct($name, $data, $dataName);
    }

    /**
     *
     */
    public function hotfixSqlite()
    {
        \Illuminate\Database\Connection::resolverFor('sqlite',
            function ($connection, $database, $prefix, $config) {
            return new class($connection, $database, $prefix, $config)
                extends SQLiteConnection {
                public function getSchemaBuilder()
                {
                    if ($this->schemaGrammar === null) {
                        $this->useDefaultSchemaGrammar();
                    }

                    return new class($this) extends SQLiteBuilder {
                        protected function createBlueprint($table, Closure $callback = null)
                        {
                            return new class($table, $callback) extends Blueprint {
                                public function dropForeign($index)
                                {
                                    return new Fluent();
                                }
                            };
                        }
                    };
                }
            };
        });
    }

    /**
     * Sign in the user and add set the authorization header with his token
     *
     * @param null $user
     * @return $this
     */
    protected function signIn($user = null): TestCase
    {
        $user = $user ?: User::factory()->create();
        $this->actingAs($user);
        $customClaims = ['user_id' => $user->id, 'company_id' => $user->company_id];
        $token = JWTAuth::fromUser($user);
        $payload = JWTAuth::manager()->getJWTProvider()->decode($token);
        $payload['user_id'] = $user->id;
        $payload['company_id'] =  $user->company_id;
        $token = JWTAuth::manager()->getJWTProvider()->encode($payload);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $this;
    }
}
