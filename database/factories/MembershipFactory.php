<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Membership;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'purchase_date' => Carbon::now()->toDateTimeString(),
            'initial_date' => Carbon::now()->format('Y-m-d'),
            'expiration_date' => Carbon::now()->addYear()->format('Y-m-d'),
            'is_active' => true,
            'company_id' => Company::factory()->create(),
            'price' => $this->faker->randomFloat(3),
            'is_frequent_payment' => true,
            'invoice_credit_note_id' => Uuid::uuid(),
            'invoice_credit_note_pdf' => $this->faker->url()
        ];
    }
}
