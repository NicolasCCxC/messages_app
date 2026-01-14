<?php

use App\Infrastructure\Formulation\UserHelper;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Models\Permission;
use App\Models\SecurityFiscalResponsibility;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddInitialData extends Migration
{


    const USERS = [
        'admin@ccxc.us' => [
            'email' => 'admin@ccxc.us',
            'id' => 'c20b36e7-9c74-4de8-b070-3aee253c0183',
            'name' => 'Admin'
        ],
        'hevillalobos@ccxc.us' => [
            'email' => 'hevillalobos@ccxc.us',
            'id' => 'db4b2170-d0e9-4bfc-8027-8a8001026068',
            'name' => 'Harold Villalobos'
        ],
        'mgallego@ccxc.us' => [
            'email' => 'mgallego@ccxc.us',
            'id' => '4dd747b3-6fc1-42b9-9030-cbc4356ef528',
            'name' => 'Michael Gallego'
        ],
        'ajquintero@ccxc.us' => [
            'email' => 'ajquintero@ccxc.us',
            'id' => '932a8026-89d5-4488-b3ff-45fb4d126609',
            'name' => 'Adan Quintero'
        ],
        'rnunez@ccxc.us' => [
            'email' => 'rnunez@ccxc.us',
            'id' => 'e8ca028e-6b46-4d46-9bd9-db78b2b20025',
            'name' => 'Ronald Nuñez'
        ],
        'mfbarrero@ccxc.us' => [
            'email' => 'mfbarrero@ccxc.us',
            'id' => 'b95f407b-c624-402a-af68-a2b86081d18e',
            'name' => 'Maria Barrero'
        ],
        'bespitia@ccxc.us' => [
            'email' => 'bespitia@ccxc.us',
            'id' => '7cd56721-bfb5-4dbb-9b84-2a2e95395527',
            'name' => 'Brian Espitia'
        ],
        'ydgonzalez@ccxc.us' => [
            'email' => 'ydgonzalez@ccxc.us',
            'id' => '651751dc-acf1-4d66-a17f-1d00e2c34124',
            'name' => 'Yeison Gonzalez'
        ],
        'csanchez@ccxc.us' => [
            'email' => 'csanchez@ccxc.us',
            'id' => '95a33969-0039-41cd-957d-992095603bf8',
            'name' => 'Camilo Sanchez'
        ],
        'jgaviria@ccxc.us' => [
            'email' => 'jgaviria@ccxc.us',
            'id' => '38ed4a46-7b95-4b09-a09b-a4b6229cbec4',
            'name' => 'Jhonier Gaviria'
        ],
        'vatroya@ccxc.us' => [
            'email' => 'vatroya@ccxc.us',
            'id' => 'ec5181e4-eecb-487c-8608-eea954f3705f',
            'name' => 'Victoria Troya'
        ],
        'jsoler@ccxc.us' => [
            'email' => 'jsoler@ccxc.us',
            'id' => '3c280290-d269-42c7-9091-0749d74de406',
            'name' => 'Jose Soler'
        ],
        'dvanegas@ccxc.us' => [
            'email' => 'dvanegas@ccxc.us',
            'id' => 'fd3d13de-0224-4d8a-823b-06b53fdf8311',
            'name' => 'David Vanegas'
        ],
        'mcmurcia@ccxc.us' => [
            'email' => 'mcmurcia@ccxc.us',
            'id' => '00ba2379-8913-4e90-89c2-a43362a88220',
            'name' => 'Maria Murcia'
        ],
        'sbejarano@ccxc.us' => [
            'email' => 'sbejarano@ccxc.us',
            'id' => '3c14e0ea-73fb-4e16-bd5a-e7bd9082e2f5',
            'name' => 'Sara Bejarano'
        ],
        'ncastro@ccxc.us' => [
            'email' => 'ncastro@ccxc.us',
            'id' => '2237a088-68bd-47dc-bad0-c3af8162eebd',
            'name' => 'Nathalia Castro'
        ],
        'varcila@ccxc.us' => [
            'email' => 'varcila@ccxc.us',
            'id' => '486ea9e0-da02-415c-aa98-30f5728e924d',
            'name' => 'Viviana Arcila'
        ],
        'marevalo@ccxc.us' => [
            'email' => 'marevalo@ccxc.us',
            'id' => '486ef9e0-da02-415c-aa98-30f5728e924d',
            'name' => 'Miguel Arevalo'
        ],
        'sespinosa@ccxc.us' => [
            'email' => 'sespinosa@ccxc.us',
            'id' => '486ef9e0-da02-415c-aa98-30f5728e924c',
            'name' => 'Said Espinoza'
        ], 'dsanchez@ccxc.us' => [
            'email' => 'dsanchez@ccxc.us',
            'id' => '486ef9e0-da02-415c-aa98-31f5728e924d',
            'name' => 'Daniela Sanchez'
        ], 'jcvalderrama@ccxc.us' => [
            'email' => 'jcvalderrama@ccxc.us',
            'id' => 'a6e304f0-c727-31f7-a15a-d2a76c06c388',
            'name' => 'Juan Camilo Valderrama'
        ],
        'dcpinto@ccxc.us' => [
            'email' => 'dcpinto@ccxc.us',
            'id' => '7c0412d9-43f4-442c-a15c-397862a2656d',
            'name' => 'Diana Pinto'
        ],
        'tmolina@ccxc.us' => [
            'email' => 'tmolina@ccxc.us',
            'id' => '0a1f9b80-9ba2-4c46-aa81-ca5f36d6d3d7',
            'name' => 'Tatiana Molina'
        ],
        'lnarvaez@ccxc.us' => [
            'email' => 'lnarvaez@ccxc.us',
            'id' => 'd2177dd1-87cb-4576-b364-408cc28081a9',
            'name' => 'Lucas Narvaez'
        ],
        'sfranco@ccxc.us' => [
            'email' => 'sfranco@ccxc.us',
            'id' => 'a1772f4e-5ae0-4e8d-831e-c829667c4dab',
            'name' => 'Silvia Franco'
        ],
        'amotta@ccxc.us' => [
            'email' => 'amotta@ccxc.us',
            'id' => 'e3bef1c3-fd8a-4b8c-b5c6-92b5e8b38738',
            'name' => 'Alejandro Motta'
        ],
        'dbello@ccxc.us' => [
            'email' => 'dbello@ccxc.us',
            'id' => 'a9236071-35cb-48fa-b462-9f31b3a1aadf',
            'name' => 'Daniel Bello'
        ],
        'lmvalbuena@ccxc.us' => [
            'email' => 'lmvalbuena@ccxc.us',
            'id' => 'f79bc9a2-ce1d-398e-b91c-4a2ffe3e4c6f',
            'name' => 'Marcela Valbuena'
        ],
        'fgonzalez@ccxc.us' => [
            'email' => 'fgonzalez@ccxc.us',
            'id' => '729b9c2a-3736-3b25-9a40-38583d3839a4',
            'name' => 'Francisco Gonzalez'
        ],
        'lgonzalez@ccxc.us' => [
            'email' => 'lgonzalez@ccxc.us',
            'id' => '847f2282-2442-3b75-a0f9-327b1464b221',
            'name' => 'Luis Gonzalez'
        ],
        'msanchez@ccxc.us' => [
            'email' => 'msanchez@ccxc.us',
            'id' => '847f2282-2442-3b75-a0f9-347b1464b221',
            'name' => 'Camila Sánchez'
        ],
        'ghernandez@ccxc.us' => [
            'email' => 'ghernandez@ccxc.us',
            'id' => '847f2282-2442-3b75-a0f9-345b1464b221',
            'name' => 'Gabriela Hernandez'
        ],
          'dlombo@ccxc.us' => [
            'email' => 'dlombo@ccxc.us',
            'id' => '847f2282-2442-3b75-a0f9-235b1464b221',
            'name' => 'Daniela Lombo'
        ],
        'dpardo@ccxc.us' => [
            'email' => 'dpardo@ccxc.us',
            'id' => 'c4024910-a753-43a1-995d-e927c5846e6a',
            'name' => 'Diana Pardo'
        ],
        'cnarvaez@ccxc.us' => [
            'email' => 'cnarvaez@ccxc.us',
            'id' => '95a33969-0039-42cd-957d-992096603bf8',
            'name' => 'Cristian Camilo'
        ],
        'lreyes@ccxc.us' => [
            'email' => 'lreyes@ccxc.us',
            'id' => '95a33369-0039-42cd-957d-992096603bf8',
            'name' => 'Luis Reyes'
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ccxc = [
            'id' => Company::COMPANY_CCXC,
            'name' => 'Centro de Consultoría para la Competitividad CCxC',
            'person_type' => Company::LEGAL_PERSON,
            'document_type' => '80fc8d67-9a2b-3027-9eae-09db2d46dfd1',
            'document_number' => '901084754',
            'company_representative_name' => 'Natalia Castro',
            'phone' => '7943044',
            'country_id' => 46,
            'country_name' => 'Colombia',
            'department_id' => 5,
            'department_name' => 'Bogotá',
            'city_id' => 149,
            'city_name' => 'Bogotá, D.c. ',
            'postal_code' => '111111',
            'address' => 'Cra 16 # 93-78 Of. 807',
            'domain' => 'ccxc-diggy.diggipymes.co',
            'make_web_page_type' => Company::LEGAL_PERSON,
            'brand_established_service' => false,
            'accept_company_privacy' => true,
            'company_privacy_acceptation_date' => now(),
            'tax_detail' => '1',
            'foreign_exchange_id' => '0e2346cd-2d32-3383-a762-203a9c013b02',
            'foreign_exchange_code' => 'COP',
            'whatsapp' => '3102102656',
            'users_available' => 100,
        ];

        $fiscalResposibilities = [
            [
                'id' => Str::uuid()->toString(),
                'code_fiscal_responsibility' => 5,
                'company_id' => $ccxc['id'],
            ],
        ];


        $roles = [
            [
                'name' => "Administrador",
                'permissions' => Permission::all()->values()
            ]
        ];

        $membership = [
            'purchase_date' => Carbon::now()->toDateTimeString(),
            'initial_date' => Carbon::now()->toDateString(),
            'expiration_date' => Carbon::now()->addYear()->toDateString(),
            'is_active' => true,
            'company_id' => Company::COMPANY_CCXC,
            'price' => 140000,
            'payment_method' => 'FREE',
            'payment_status' => 'APPROVED',
        ];

        Company::query()->insert($ccxc);

        Membership::create($membership);


        $membership = Membership::first();
        $invoiceId =  Str::uuid()->toString();
        $webDesign = Str::uuid()->toString();
        $modules = [
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 1
            ],
            [
                'id' => $webDesign,
                'membership_id' => $membership->id,
                'membership_modules_id' => 2
            ],
            [
                'id' => $invoiceId,
                'membership_id' => $membership->id,
                'membership_modules_id' => 3
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 4
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 5
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 6
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 7
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 8
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 9
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 10
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 11
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 12
            ],
            [
                'id' => Str::uuid()->toString(),
                'membership_id' => $membership->id,
                'membership_modules_id' => 13
            ],

        ];

        MembershipHasModules::insert($modules);

        $submodules = [
          ['id'=> Str::uuid()->toString(), 'membership_has_modules_id' => $invoiceId, 'sub_module_id' => 4],
          ['id'=> Str::uuid()->toString(), 'membership_has_modules_id' => $webDesign, 'sub_module_id' => 9]
        ];

        MembershipSubModule::insert($submodules);

        SecurityFiscalResponsibility::query()->insert($fiscalResposibilities);

        collect(self::USERS)->each(function ($item, $key) use ($ccxc, $roles) {
            $user = User::create([
                'id' => $item['id'],
                'email' => $item['email'],
                'company_id' => $ccxc['id'],
                'name' => $item['name'],
                'password' => Hash::make('@!F+CCxC2@2@+E!@'),
                'accept_data_policy' => true,
                'accept_terms_conditions' => true,
                'user_privacy_acceptation_date' => now()
            ]);
            if ($key === 'admin@ccxc.us') {
                $mainRol = [
                    [
                        'name' => Role::Main,
                        'permissions' => Permission::all()->values()
                    ]
                ];
                UserHelper::assignRolesAndPermissions($user, $mainRol);
            } else {
                UserHelper::assignRolesAndPermissions($user, $roles);
            }
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->truncate();
        DB::table('companies')->truncate();
        DB::table('roles')->truncate();
        DB::table('roles_permissions')->truncate();
        DB::table('fiscal_responsibilities')->truncate();
        Membership::truncate();

    }
}
