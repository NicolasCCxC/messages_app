<?php

namespace Database\Factories;

use App\Models\PointSale;
use App\Models\PhysicalStore;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointSaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PointSale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'physical_store_id' => PhysicalStore::factory(),
            'name' => $this->faker->name(),
            'contact_link' => $this->faker->domainName,
        ];
    }
}
