<?php

namespace Database\Factories;

use App\Models\CancelModulesDetail;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use Illuminate\Database\Eloquent\Factories\Factory;

class CancelModulesDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CancelModulesDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'membership_has_modules_id' => MembershipHasModules::factory()->create(),
            'reason' => $this->faker->text(100),
            'company_id' => Company::factory()->create(),
            'membership_id' => Membership::factory()->create(),
        ];
    }
}
