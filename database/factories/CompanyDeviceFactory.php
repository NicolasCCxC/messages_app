<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyDevice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Provider\Uuid;

class CompanyDeviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyDevice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'name' => $this->faker->text(),
            'company_id' => Company::inRandomOrder()->first(),
        ];
    }
}
