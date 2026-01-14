<?php

namespace Database\Factories;

use App\Models\CompanyInformation;
use App\Models\CompanyPaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class CompanyPaymentGatewayFactory extends Factory
{
    protected $model = CompanyPaymentGateway::class;

    public function definition(): array
    {
        return [
            "payment_gateway_id" => 1,
            "credentials" => $this->encryptKeys([
                "api_key" => "4Vj8eK4rloUd272L48hsrarnUA",
                "api_login" => "pRRXKOl8ikMmt9u",
                "public_key" => "PKaC6H4cEDJD919n705L544kSU",
                "merchant_id" => "508029",
                "account_id" => "512321"
            ]),
            "date" => "1638366127",
            "company_information_id" => CompanyInformation::factory()->create(["company_id" => "83e80ae5-affc-32b4-b11d-b4cab371c48b"])
        ];
    }

    private function encryptKeys(array $data): array
    {
        return collect($data)->map(function ($item) {
            return  Crypt::encrypt($item);
        })->toArray();
    }
}
