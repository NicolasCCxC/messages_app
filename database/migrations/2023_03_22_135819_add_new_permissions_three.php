<?php

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNewPermissionsThree extends Migration
{
    private $permissions;

    public function __construct()
    {
        $this->permissions = [
            [
                'id' => 'c995fce2-1bc9-4277-8f8a-8e6a38b85f94',
                'name' => 'Registro de abonos: documentos por cobrar',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT,
                'index' => 5
            ],
            [
                'id' => '125ff47f-198c-4495-8147-0a2c28e454c9',
                'name' => 'Kardex movimiento diario de inventario',
                'description' => Permission::SUBMODULE_WEB_DESING,
                'index' => 4
            ],
            [
                'id' => '493a1642-a6b6-4667-99f6-14a2ba5ade78',
                'name' => 'Informe de ingresos',
                'description' => Permission::SUBMODULE_WEB_DESING,
                'index' => 5
            ],
            [
                'id' => '801a8c82-e8ca-4604-bb75-032d0b1798ea',
                'name' => 'Factura de compra',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT,
                'index' => 5
            ],
            [
                'id' => '9bcb803e-c112-4f00-8104-29f422ac24b9',
                'name' => 'Registro de abonos',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT,
                'index' => 6
            ],
            [
                'id' => 'bf597a15-193d-4a5d-a6aa-32dbf3e07aba',
                'name' => 'Reporte de compra',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT,
                'index' => 7
            ],
            [
                'id' => 'fdffc532-9df5-432b-a2ef-04e0c9bba39b',
                'name' => 'Reporte de registro de abonos',
                'description' => Permission::SUBMODULE_SYSTEM_MANAGEMENT,
                'index' => 8
            ],
            [
                'id' => '46ee42fa-39dc-407e-9c58-5dd9a1beae97',
                'name' => 'Kardex movimiento diario de inventario',
                'description' => Permission::MODULE_ELECTRONIC_BILLING,
                'index' => 5
            ],
            [
                'id' => 'd9e91b0e-55ba-40ce-9a33-9054e2deb51b',
                'name' => 'Informe de ingresos',
                'description' => Permission::MODULE_ELECTRONIC_BILLING,
                'index' => 6
            ],
            [
                'id' => 'b8f09f2d-3cd2-4355-b80d-067a23e7946b',
                'name' => 'Documentos soporte',
                'description' => Permission::MODULE_ELECTRONIC_BILLING,
                'index' => 11
            ],
            [
                'id' => '56ecfc93-72da-46d2-8d8e-b11e4ac57e62',
                'name' => 'Factura de compra',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                'index' => 1
            ],
            [
                'id' => 'e370e29e-2d9f-4024-9def-6f6aa9fbd44b',
                'name' => 'Actualización bodegas',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                'index' => 3
            ],
            [
                'id' => '82e9f1a9-d769-406a-bbb9-ea107210cd62',
                'name' => 'Resumen de compras',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                'index' => 4
            ],
            [
                'id' => 'a8fcb477-c9c7-4a20-89b6-7a70d3f74616',
                'name' => 'Informe de egresos',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                'index' => 5
            ],
            [
                'id' => 'ef2a02f1-5aa0-44d2-b725-242d6062eb0b',
                'name' => 'Proveedores',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                'index' => 10
            ],
            [
                'id' => '9fb020a8-e343-4de8-895d-e33dfdbee502',
                'name' => 'Reporte de compra',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                'index' => 11
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
