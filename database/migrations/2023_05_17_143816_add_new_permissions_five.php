<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class AddNewPermissionsFive extends Migration
{
    private $permissions;

    public function __construct()
    {
        // Documento soporte
        $this->permissions = [
            [
                'id' => 'a3d0a85a-b152-476e-9524-58286cae5618',
                'name' => 'Instrucciones',
                'description' => Permission::MODULE_DOCUMENT_SUPPORT,
                'index' => 0
            ],
            [
                'id' => 'e1a50954-3470-46d8-932b-d04d46cb8ffc',
                'name' => 'Información requerida para el documento soporte',
                'description' => Permission::MODULE_DOCUMENT_SUPPORT,
                'index' => 1
            ],
            [
                'id' => 'c7db7925-b3a7-4a59-b47d-d7db45a7c154',
                'name' => 'Crear documento soporte',
                'description' => Permission::MODULE_DOCUMENT_SUPPORT,
                'index' => 2
            ],
            [
                'id' => '62e27114-50e2-4301-8bbf-a2c740cdf1be',
                'name' => 'Crear notas de ajuste',
                'description' => Permission::MODULE_DOCUMENT_SUPPORT,
                'index' => 3
            ],
            [
                'id' => 'd0c7f3b5-a867-4ec8-9c9b-7eae0858c606',
                'name' => 'Reporte de documentos emitidos',
                'description' => Permission::MODULE_DOCUMENT_SUPPORT,
                'index' => 4
            ],
            [
                'id' => '7c6c2137-3bdd-4eb7-b5d7-d13a1f59f947',
                'name' => 'Configuración de notificaciones',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_DOCUMENT_SUPPORT,
                'index' => 1
            ],
            [
                'id' => 'd073131e-d7ce-4ae4-a0f2-1c6a9f734b64',
                'name' => 'Notificaciones diarias',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_DOCUMENT_SUPPORT,
                'index' => 2
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
        Permission::find('a8b9749e-a04f-436a-827c-b8cd35c71e78')->delete();
        Permission::find('79ba277d-4856-4873-80f6-5cbf8ada2f13')->delete();
        Permission::query()->insert($this->permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissionsCreate = [
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
        foreach ($permissionsCreate as $value) {
            Permission::insert($value);
        }
        Permission::query()->findMany(collect($this->permissions)->pluck('id'))->each(fn($permission) => $permission->delete());
    }
}
