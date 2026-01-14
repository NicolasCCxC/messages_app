<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class UpdatePermissionsTwo extends Migration
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
                'id' => 'dfc3a209-993e-402a-a3c4-a6431bce0ca1',
                'name' => 'Catálogo de productos y/o servicios'
            ]
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
        $permissions = collect ([
            [
                'id' => 'dfc3a209-993e-402a-a3c4-a6431bce0ca1',
                'name' => 'Catálogo productos/servicios'
            ]
        ]);

        $permissions->each(function($permission) {
            Permission::query()->find($permission['id'])->update($permission);
        });

    }
}
