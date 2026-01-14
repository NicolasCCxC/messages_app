<?php

namespace Database\Factories;

use App\Models\PhysicalStore;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class PhysicalStoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PhysicalStore::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'company_id' => Company::COMPANY_CCXC,
            'name' => $this->faker->name(),
            'address' => $this->faker->address,
            'phone' => $this->faker->numberBetween(1000000000, 9999999999),
            'country_id' => $this->faker->numberBetween(1, 9999),
            'country_name' => $this->faker->country,
            'department_id' => $this->faker->numberBetween(1, 9999),
            'department_name' => $this->faker->city,
            'city_id' => $this->faker->numberBetween(1, 9999),
            'city_name' => $this->faker->city
        ];
    }
}
