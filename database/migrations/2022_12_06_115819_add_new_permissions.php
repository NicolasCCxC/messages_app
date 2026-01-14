<?php

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNewPermissions extends Migration
{
    private $permissions;

    public function __construct()
    {
        $this->permissions = [
            [
                'id' => '137ab5df-99b2-4198-94cf-bc006e97e7b0',
                'name' => 'Armar bodegas',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => 'e3200c57-f704-4010-a590-a73511333940',
                'name' => 'Información de la prestación de servicios',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ]
        ];
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::query()->insert($this->permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::query()->findMany(collect($this->permissions)->pluck('id'))->each(fn($permission) => $permission->delete());
    }
}
