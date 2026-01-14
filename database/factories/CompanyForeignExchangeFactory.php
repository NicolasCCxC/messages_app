<?php

namespace Database\Factories;

use Faker\Provider\Uuid;
use App\Models\CompanyForeignExchange;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyForeignExchangeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyForeignExchange::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'foreign_exchange_id' => Uuid::uuid(),
            'company_id' => Company::COMPANY_CCXC,
            'is_active' => true,
        ];
    }
}
