<?php

namespace Database\Factories;

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
    	return [
    	    'id' => Uuid::uuid(),
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'index' => 0,
    	];
    }
}
