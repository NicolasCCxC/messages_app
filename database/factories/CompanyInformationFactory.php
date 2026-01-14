<?php

namespace Database\Factories;

use App\Enums\CompanyInformation as EnumsCompanyInformation;
use App\Models\CompanyInformation;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyInformationFactory extends Factory
{
    protected $model = CompanyInformation::class;

    public function definition(): array
    {
        return [
            'company_id' => Uuid::uuid(),
            'payment_information' => json_encode(EnumsCompanyInformation::PAYMENT_INFORMATION)
        ];
    }
}
