<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Role;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
    	return [
            'id' => Uuid::uuid(),
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
    	    'company_id' => Company::inRandomOrder()->first(),
    	];
    }
}
