<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class AddNewPermissionsFour extends Migration
{
    private $permissions;

    public function __construct()
    {
        $this->permissions = [
            [
                'id' => 'a8b9749e-a04f-436a-827c-b8cd35c71e78',
                'name' => 'Crear documento soporte',
                'description' => Permission::SUBMODULE_DOCUMENT_SUPPORT,
                'index' => 1
            ],
            [
                'id' => '79ba277d-4856-4873-80f6-5cbf8ada2f13',
                'name' => 'Creación notas de ajuste',
                'description' => Permission::SUBMODULE_DOCUMENT_SUPPORT,
                'index' => 2
            ],
            
        ];
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::find('b8f09f2d-3cd2-4355-b80d-067a23e7946b')->delete();
        Permission::query()->insert($this->permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::insert([
            'id' => 'b8f09f2d-3cd2-4355-b80d-067a23e7946b',
            'name' => 'Documentos soporte',
            'description' => Permission::MODULE_ELECTRONIC_BILLING,
            'index' => 11
        ]);
        Permission::query()->findMany(collect($this->permissions)->pluck('id'))->each(fn($permission) => $permission->delete());
    }
}
