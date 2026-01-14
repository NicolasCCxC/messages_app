<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\MembershipHasModules;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipHasModulesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MembershipHasModules::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'membership_id' => Membership::factory()->create(),
            'membership_modules_id' => $this->faker->numberBetween(1, 13),
            'is_active' => true,
            'percentage_discount' => 0,
            'is_frequent_payment' => false,
            'expiration_date' => Carbon::now()->addYear()->toDateString(),
        ];
    }
}
