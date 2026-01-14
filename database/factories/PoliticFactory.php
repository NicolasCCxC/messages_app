<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Politic;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoliticFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Politic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(Politic::LIST_POLITICS),
            'company_id' => Company::inRandomOrder()->first(),
            'bucket_details_id' => Uuid::uuid()
        ];
    }
}
