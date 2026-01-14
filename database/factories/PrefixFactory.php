<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Prefix;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrefixFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Prefix::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'resolution_number' => $this->faker->numberBetween(1000, 20000000000000),
            'type' => $this->faker->randomElement(Prefix::TYPE),
            'prefix' => $this->faker->regexify('[A-Z]{4}'),
            'initial_validity' => $this->faker->dateTimeInInterval('-1 years', '+2 days'),
            'final_validity' => $this->faker->dateTimeInInterval('-2 days', '+1 years'),
            'final_authorization_range' => $this->faker->numberBetween(999000000, 999999999),
            'initial_authorization_range' => $this->faker->numberBetween(900000000, 999000000),
            'resolution_technical_key' => $this->faker->uuid(),
            'physical_store' => $this->faker->boolean(),
            'website' => $this->faker->boolean(),
            'contingency' => $this->faker->boolean(),
            'company_id' => Company::factory()->create()
        ];
    }

    /**
     * Indicate that the Prefix is default.
     *
     * @return Factory
     */
    public function default()
    {
        return $this->state(function (array $attributes) {
            return [
                'physical_store' => false,
            ];
        });
    }

     /**
     * Indicate that the Prefix is default.
     *
     * @return Factory
     */
    public function service()
    {
        return $this->state(function (array $attributes) {
            return [
                'contingency' => true,
            ];
        });
    }

    /**
     * Indicate that the Prefix is default.
     *
     * @return Factory
     */
    public function website()
    {
        return $this->state(function (array $attributes) {
            return [
                'website' => true,
            ];
        });
    }
}
