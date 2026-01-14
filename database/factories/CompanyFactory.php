<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Company;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{

    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::uuid(),
            'name' => $this->faker->company,
            'person_type' => Company::NATURAL_PERSON,
            'document_type' => Uuid::uuid(),
            'foreign_exchange_id' => Uuid::uuid(),
            'foreign_exchange_code' => $this->faker->word(),
            'document_number' => (string)$this->faker->numberBetween(1000000000, 9999999999),
            'company_representative_name' => $this->faker->name(),
            'phone' => $this->faker->numberBetween(1000000000, 9999999999),
            'country_id' => $this->faker->numberBetween(1, 9999),
            'country_name' => $this->faker->country,
            'department_id' => $this->faker->numberBetween(1, 9999),
            'department_name' => $this->faker->city,
            'city_id' => $this->faker->numberBetween(1, 9999),
            'city_name' => $this->faker->city,
            'postal_code' => $this->faker->numberBetween(1000000, 9999999),
            'address' => $this->faker->address,
            'domain' => $this->faker->domainName,
            'make_web_page_type' => Company::NATURAL_PERSON,
            'brand_established_service' => true,
            'accept_company_privacy' => true,
            'has_a_physical_store' => false,
            'has_e_commerce' => false,
            'company_privacy_acceptation_date' => now(),
            'whatsapp' => $this->faker->numberBetween(1000000000, 9999999999),
            'users_available' => 3,
            'is_billing_us' => true,
        ];
    }

    /**
     * Assign the person_type to Legal
     *
     * @return CompanyFactory
     */
    public function legalPerson(): CompanyFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'person_type' => Company::LEGAL_PERSON
            ];
        });
    }
}
