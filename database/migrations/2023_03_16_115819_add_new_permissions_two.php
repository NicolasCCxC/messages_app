<?php

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNewPermissionsTwo extends Migration
{
    private $permissions;

    public function __construct()
    {
        $this->permissions = [
            [
                'id' => '59b56929-51e4-4376-bd1f-efae1957bd3b',
                'name' => 'Armar base de datos clientes',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT
            ],
            [
                'id' => '7aa53544-0ef8-4f8e-9bbd-df748754cf18',
                'name' => 'Armar base de datos proveedores',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT
            ],
            [
                'id' => 'dfc3a209-993e-402a-a3c4-a6431bce0ca1',
                'name' => 'Catálogo productos/servicios',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT
            ],
            [
                'id' => '9c305a21-e62c-4efc-8575-d8d2d4e2dc72',
                'name' => 'Cotizaciones',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT
            ],
            [
                'id' => '8f5fbaec-2c4e-4306-97b2-8d7020aa653f',
                'name' => 'Conciliaciones',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT
            ],
            [
                'id' => 'a4a36373-e441-4855-a19f-3fd66d123389',
                'name' => 'Herramienta de control de inventarios para los vendedores: POS',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT
            ],
            [
                'id' => '02695dcf-1fe9-48d4-b0f7-7c69b5852d83',
                'name' => 'Calendario',
                'description' => Permission::MODULE_PLANNING_ORGANIZATION
            ],
            [
                'id' => '6c97d7a3-a585-4c42-a727-754c4aeb5a77',
                'name' => 'Diagrama de Gantt',
                'description' => Permission::MODULE_PLANNING_ORGANIZATION
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
