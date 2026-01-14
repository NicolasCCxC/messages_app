<?php

namespace Database\Factories;

use App\Models\PrivacyPurpose;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Provider\Uuid;

class PrivacyPurposeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrivacyPurpose::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    	return [
    	    'id' => Uuid::uuid(),
    	    'description' => $this->faker->text(),
    	    'is_default' => false,
    	];
    }
}
