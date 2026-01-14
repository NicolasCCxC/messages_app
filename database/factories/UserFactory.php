<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

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
            'type' => $this->faker->word(),
            'document_number' => (string) $this->faker->numberBetween(1000000000, 9999999999),
            'document_type' => Uuid::uuid(),
            'company_id' => Company::inRandomOrder()->first(),
            'accept_data_policy' => true,
            'accept_terms_conditions' => true,
            'user_privacy_acceptation_date' => now(),
            'user_terms_conditions_acceptation_date' => now(),
            'is_first_login' => true
        ];
    }
}
