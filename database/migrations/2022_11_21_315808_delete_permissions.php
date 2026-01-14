<?php

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DeletePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = collect([
            [
                'id' => '7b2d49d0-421a-3c14-bafe-98b86ca30cf7',
                'name' => 'Listado de productos/servicios agregados',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => '457527f4-3ccd-30ca-9124-97a7d4471778',
                'name' => 'Listado de productos/servicios editados',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => '1ba73ce5-2e25-33b0-8278-3646b5563f80',
                'name' => 'Listado de productos/servicios eliminados',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
        ]);

        Permission::query()->findMany($permissions->pluck('id'))->each(fn($permission) => $permission->delete());
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
