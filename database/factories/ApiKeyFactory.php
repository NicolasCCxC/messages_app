<?php

namespace Database\Factories;

use App\Models\ApiKey;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiKeyFactory extends Factory
{

    use HasFactory;

    
    protected $model = ApiKey::class;

    public function definition(): array
    {
    	return [
    	    'id' => Uuid::uuid(),
            'auth_key' => $this->faker->text(),
            'security_key' => $this->faker->text(),
            'name' => $this->faker->name()
    	];
    }
}
