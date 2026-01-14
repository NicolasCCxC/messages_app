<?php

namespace Database\Seeders;

use App\Infrastructure\Formulation\UserHelper;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WebsiteUsersSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminWebsite = [
            'id' => '4614af9c-f662-4839-a678-1e046d2faa4e',
            'name' => 'Empresa de Prueba',
            'person_type' => Company::LEGAL_PERSON,
            'document_type' => '80fc8d67-9a2b-3027-9eae-09db2d46dfd1',
            'document_number' => '21752698',
            'company_representative_name' => 'Laura Alvarez',
            'phone' => '7943044',
            'country_id' => 46,
            'country_name' => 'Colombia',
            'department_id' => 5,
            'department_name' => 'Bogotá',
            'city_id' => 149,
            'city_name' => 'Bogotá, D.c. ',
            'postal_code' => '111111',
            'address' => 'Cra 16 # 93-78 Of. 807',
            'domain' => 'app-famiefi-ccxc.co',
            'make_web_page_type' => Company::LEGAL_PERSON,
            'brand_established_service' => false,
            'accept_company_privacy' => true,
            'company_privacy_acceptation_date' => now(),
            'foreign_exchange_id' => '0e2346cd-2d32-3383-a762-203a9c013b02',
            'foreign_exchange_code' => 'COP',
            'whatsapp' => '3102102656'
        ];

        $company = Company::create($adminWebsite);

        $roleSuperAdmin = [
            [
                'name' => Role::Main,
                'permissions' => Permission::all()->values()
            ]
        ];

        $admin = User::create([
            'id' => 'c20b36e7-9c76-4de8-b070-3aee253c0183',
            'email' => 'adminwebsite@ccxc.us',
            'company_id' => $company->id,
            'name' => 'Admin',
            'password' => Hash::make('@!F+CCxC2@2@+E!@'),
            'accept_data_policy' => true,
            'accept_terms_conditions' => true,
            'user_privacy_acceptation_date' => now(),
            'user_terms_conditions_acceptation_date' => now()
        ]);
        UserHelper::assignRolesAndPermissions($admin, $roleSuperAdmin);

        $roleWebsiteonly = [
            [
                'name' => 'Editar',
                'permissions' => [
                    [
                        'name' => 'Información básica',
                        'description' => 'Diseño página web',
                    ],
                    [
                        'name' => 'Diseño de pestañas, imágenes y descripción de productos/servicios',
                        'description' => 'Tienda virtual: bodegas y productos/servicios',
                    ],
                ]
            ]
        ];

        $userWebsite = User::create([
            'id' => '14b4ce12-9968-3b61-882d-4a22b7a2cce2',
            'email' => 'pruebaadmin@ccxc.us',
            'company_id' => $company->id,
            'name' => 'pruebaadmin',
            'password' => Hash::make('@!F+CCxC2@2@+E!@'),
            'accept_data_policy' => true,
            'accept_terms_conditions' => true,
            'user_privacy_acceptation_date' => now(),
            'user_terms_conditions_acceptation_date' => now(),
        ]);

        UserHelper::assignRolesAndPermissions($userWebsite, $roleWebsiteonly);


    }
}
