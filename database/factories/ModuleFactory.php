<?php

namespace Database\Factories;

use App\Models\Module;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Module::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph()
        ];
    }
}
