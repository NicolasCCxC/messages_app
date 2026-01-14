<?php

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = collect ([
            [
                'id' => '53893de5-38a7-3825-b657-aa295eea100b',
                'name' => 'Registro de la empresa',
                'description' => Permission::SUBMODULE_SERVICES_INFORMATION
            ],
            [
                'id' => 'e0de8b8f-e651-37f8-b3b8-03088d8c9cff',
                'name' => 'Políticas',
                'description' => Permission::SUBMODULE_SERVICES_INFORMATION
            ],
            [
                'id' => '0be9509f-14fd-3301-934a-160f26fe85cf',
                'name' => 'Administrador de usuarios',
                'description' => Permission::SUBMODULE_SERVICES_INFORMATION
            ],
            [
                'id' => 'c81027c7-585d-384a-a74e-1a26946de073',
                'name' => 'Configuración de notificaciones',
                'description' => Permission::SUBMODULE_SERVICES_INFORMATION
            ],
            [
                'id' => '3bec9dee-87a8-3897-b07f-a4f5f7a6db28',
                'name' => 'Centro de notificaciones',
                'description' => Permission::SUBMODULE_SERVICES_INFORMATION
            ],


            [
                'id' => '956c48b0-316b-3f13-9251-a707543508f4',
                'name' => 'Armar catálogo de productos y/o servicios',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => 'e2c82a27-7cd7-3cf1-a25d-76ecc2bbeb85',
                'name' => 'Información de costo de envíos de productos',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => 'b774cda7-7be7-3b10-819f-8692c2ddc97e',
                'name' => 'Listado de catálogo de productos y/o servicios',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
        ]);

        $permissions->each(function($permission) {
            Permission::query()->find($permission['id'])->update($permission);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
