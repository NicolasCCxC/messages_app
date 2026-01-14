<?php

namespace Database\Factories;

use App\Models\PayTransaction;
use App\Models\Company;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PayTransactionFactory extends Factory
{
    protected $model = PayTransaction::class;

    public function definition()
    {
        return [
            'transaction_id' => Str::uuid()->toString(),
            'membership_id' => Membership::factory(),
            'company_id' => Company::factory(),
            'users_quantity' => $this->faker->numberBetween(0, 10),
            'invoices_quantity' => $this->faker->numberBetween(0, 10),
            'pages_quantity' => $this->faker->numberBetween(0, 50),
            'status' => $this->faker->randomElement([
                $this->model::PAYMENT_STATUS_PENDING ?? 'PENDING',
                $this->model::PAYMENT_STATUS_APPROVED ?? 'APPROVED',
                $this->model::PAYMENT_STATUS_DECLINED ?? 'DECLINED',
            ]),
            'json_invoice' => null,
            'json_pse_url_response' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}