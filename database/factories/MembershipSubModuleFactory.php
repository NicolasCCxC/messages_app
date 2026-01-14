<?php

namespace Database\Factories;


use App\Models\MembershipSubModule;
use App\Models\MembershipHasModules;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipSubModuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MembershipSubModule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'membership_has_modules_id' => MembershipHasModules::factory()->create(),
            'sub_module_id' => $this->faker->numberBetween(1, 8),
            'is_active' => true,
            'is_frequent_payment' => false,
        ];
    }
}
