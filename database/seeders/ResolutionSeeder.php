<?php

namespace Database\Seeders;

use App\Models\Prefix;
use Illuminate\Database\Seeder;
use App\Models\Company;

class ResolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resolutionInvoice = [
            'id' => '73449052-3816-3ca9-8c02-55642a3a464b',
            'company_id' => Company::COMPANY_CCXC,
            'resolution_number' => 18760000001,
            'type' => 'INVOICE',
            'prefix' => 'SETP',
            'initial_validity' => '2019-01-19',
            'final_validity' => '2030-01-19',
            'final_authorization_range' => 995000000,
            'initial_authorization_range' => 990000000,
            'physical_store' => false,
            'website' => true,
            'contingency' => false,
            'resolution_technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c'
        ];

        Prefix::factory()->create($resolutionInvoice);

        $resolutionDebitoNote = [
            'id' => 'e8d2f8ae-8fa3-4f2c-8245-b84aba548424',
            'company_id' => Company::COMPANY_CCXC,
            'resolution_number' => null,
            'type' => 'DEBIT_NOTE',
            'prefix' => 'NDTP',
            'initial_validity' => '2019-01-19',
            'final_validity' => '2030-01-19',
            'final_authorization_range' => 995000000,
            'initial_authorization_range' => 1,
            'physical_store' => false,
            'website' => false,
            'contingency' => false,
            'resolution_technical_key' => null
        ];

        Prefix::factory()->create($resolutionDebitoNote);

        $resolutionCreditNote = [
            'id' => '3ac6a406-b9d6-4237-ae21-be04778fefaf',
            'company_id' => Company::COMPANY_CCXC,
            'resolution_number' => null,
            'type' => 'CREDIT_NOTE',
            'prefix' => 'NCTP',
            'initial_validity' => '2019-01-19',
            'final_validity' => '2030-01-19',
            'final_authorization_range' => 995000000,
            'initial_authorization_range' => 1,
            'physical_store' => false,
            'website' => false,
            'contingency' => false,
            'resolution_technical_key' => null
        ];

        Prefix::factory()->create($resolutionCreditNote);
    }
}
