<?php

namespace Database\Seeders;

use App\Infrastructure\Formulation\InventoryHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Illuminate\Support\Str;

class CiiuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => Str::uuid()->toString(),
                'company_id' => Company::COMPANY_CCXC,
                'code' => '0111',
                'name' => 'Cultivo de cereales (excepto arroz), legumbres y semillas oleaginosas.',
                'ciiu_id' => 2,
                'is_main' => true
            ],
            [
                'id' => Str::uuid()->toString(),
                'company_id' => Company::COMPANY_CCXC,
                'code' => '9492',
                'name' => 'Actividades de asociaciones políticas.',
                'ciiu_id' => 718,
                'is_main' => false
            ]
        ];

        DB::table('ciius_company')->insert($data);

        InventoryHelper::updateCategoriesAndProductTypesDefault($data,Company::COMPANY_CCXC);
    }
}
