<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'password' => Hash::make('password'),
            'document_number' => (string) $this->faker->numberBetween(1000000000, 9999999999),
            'document_type' => Uuid::uuid(),
            'tax_details_name' => $this->faker->name(),
            'tax_details_code' => $this->faker->name(),
        ];
    }
}
