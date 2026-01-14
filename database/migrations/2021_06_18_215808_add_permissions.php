<?php

use App\Models\Permission;
use Faker\Provider\Uuid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
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
                'name' => 'Armar catálogo de productos/servicios',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => 'e2c82a27-7cd7-3cf1-a25d-76ecc2bbeb85',
                'name' => 'Información de costo de envíos de productos',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
            [
                'id' => 'b774cda7-7be7-3b10-819f-8692c2ddc97e',
                'name' => 'Listado de catálogo de productos/servicios',
                'description' => Permission::SUBMODULE_PRODUCT_SERVICES
            ],
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

            [
                'id' => 'd032b068-62f8-3656-94bc-c56686e061e3',
                'name' => 'Dominio',
                'description' => Permission::SUBMODULE_WEB_DESING
            ],
            [
                'id' => 'f4d9a3c6-6fbc-30a7-849d-7bc794246f65',
                'name' => 'Información básica',
                'description' => Permission::SUBMODULE_WEB_DESING
            ],
            [
                'id' => 'ff642d1f-2cf6-3287-9771-e785ea1a89a4',
                'name' => 'Pasarelas de pago',
                'description' => Permission::SUBMODULE_WEB_DESING
            ],
            [
                'id' => 'ff642d1f-2cf6-3287-9701-e785ea1a89a4',
                'name' => 'Tienda virtual: productos/servicios',
                'description' => Permission::SUBMODULE_WEB_DESING
            ],


            [
                'id' => 'ff642d1f-2cf9-3287-9701-e785ea1a89a4',
                'name' => 'Configuración de notificaciones',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_DESING
            ],
            [
                'id' => 'fe641d1f-2cf9-3287-9701-e785ea1a89a4',
                'name' => 'Notificaciones diarias',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_DESING
            ],
            [
                'id' => 'fe641d1f-2cf9-3287-9701-e785ea1a89a7',
                'name' => 'Verificaciones',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_DESING
            ],


            [
                'id' => '6cff87f1-9f98-38e1-a27e-953f003f3448',
                'name' => 'Instrucciones',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => '6cff87f1-9f88-38e1-a27e-953f003f3448',
                'name' => 'Parametrización y personalización factura electrónica',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => '6cff87f1-9f98-38e1-a27e-953f003f1448',
                'name' => 'Crear factura electrónica de venta',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => 'fb04e247-2a50-3a9b-b7bf-ef6b3e610d30',
                'name' => 'Documentos electrónicos que requieren acción',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => '20901b42-e7bb-332e-ba2d-e2bc499eb3b8',
                'name' => 'Registro de abonos',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => 'dfdf5953-b5ed-3354-9238-10fce725fa66',
                'name' => 'Reporte de registro de abonos',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => 'a9a4cb45-c682-30e0-b038-bd47c2bd406e',
                'name' => 'Reporte de documentos emitidos',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => '91f3c7d2-c8a3-3f00-86db-1e71816bcd4e',
                'name' => 'Si tiene otro proveedor tecnológico: Importar documento XML',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],
            [
                'id' => 'efb1019c-0136-470b-8c51-8d95ebd6deee',
                'name' => 'Creación de notas débito/notas crédito',
                'description' => Permission::MODULE_ELECTRONIC_BILLING
            ],


            [
                'id' => '0c7689b9-f341-3f5d-959b-d8d0b4db19c5',
                'name' => 'Configuración de notificaciones',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_BILLING
            ],
            [
                'id' => '5f4f2ad9-9e7c-3b73-b6a8-5716d3b96c77',
                'name' => 'Notificaciones diarias',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_BILLING
            ],


            [
                'id' => 'd3f13d77-eb92-3ece-ae5e-1ce37c215842',
                'name' => 'Armar bodegas',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL
            ],
            [
                'id' => '700e42dc-aa22-3276-9aa9-21430a295a91',
                'name' => 'Capacidad máxima de productos por bodegas',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL
            ],
            [
                'id' => 'dec15bc6-4cb0-3f55-9197-b9bf5d49b606',
                'name' => 'Traslado entre bodegas para distribución de inventario',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL
            ],
            [
                'id' => '2e0f3837-6c8f-312a-a779-f09e220fd866',
                'name' => 'Listado traslado entre bodegas',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL
            ],
            [
                'id' => '276bb913-1bf1-3065-ab71-1f1bb6d7e951',
                'name' => 'Nivel mínimo de reabastecimiento por producto por bodega',
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL
            ],


            [
                'id' => '2a8a0e72-1406-3a85-8ff6-a8982f82466b',
                'name' => 'Kardex movimiento diario de inventario',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => '9d1d478f-5f9d-32b1-9a21-7619176b88fa',
                'name' => 'Control diario de inventario por bodega por producto',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => '1d539578-631d-35b2-923d-14b3a2b5c02e',
                'name' => 'Cantidades disponibles por producto por bodega',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => 'e9e54ec1-41ca-3ecc-9986-8d13a9f7c37d',
                'name' => 'Control de inventario total por producto por canal de venta',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => '0d867f46-ce52-3d24-9813-22415e149f38',
                'name' => 'Control de capacidad máxima de productos por bodegas',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => '51de4b03-b15e-3043-ae45-34b80bbd22f5',
                'name' => 'Control de nivel mínimo de reabastecimiento por producto por bodega',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => 'd27bd81b-98f5-3b5f-ad53-8c096aa40608',
                'name' => 'Tutorial cálculo de costo unitario inicial',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],
            [
                'id' => '86ac7739-4a53-4688-a8ef-a9b1c6ff2b8e',
                'name' => 'Tutorial cálculo de valor unitario inicial',
                'description' => Permission::SUBMODULE_INVENTORY_MANAGE
            ],


            [
                'id' => '53de4b03-b15e-3043-ae45-34b80bbd22f5',
                'name' => 'Configuración de notificaciones',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_INVENTORY
            ],
            [
                'id' => 'd27bd86b-98f5-3b5f-ad53-8c096aa40608',
                'name' => 'Notificaciones diarias',
                'description' => Permission::SUBMODULE_MANAGE_NOTIFICATION_INVENTORY
            ],


            [
                'id' => '660018b8-945d-3e71-8b67-0714cc37bb95',
                'name' => 'Reporte histórico de valor unitario (precio de venta) por producto',
                'description' => Permission::SUBMODULE_PRODUCT_MANAGEMENT
            ],
            [
                'id' => 'dffd2e8b-898a-3568-b77a-10eeb2267ff5',
                'name' => 'Análisis dinámico de productos/servicios',
                'description' => Permission::SUBMODULE_PRODUCT_MANAGEMENT
            ],
            [
                'id' => '8c446573-ee3b-3896-96fa-3694c72f7e59',
                'name' => 'Portafolio óptimo de productos/servicios',
                'description' => Permission::SUBMODULE_PRODUCT_MANAGEMENT
            ],
            [
                'id' => '9d32fbd3-08d0-33b7-9297-d7794f3b7b80',
                'name' => 'Reporte de rentabilidades',
                'description' => Permission::SUBMODULE_PRODUCT_MANAGEMENT
            ],
            [
                'id' => 'fab72e35-7197-339b-872a-7563181084ce',
                'name' => 'Reporte de ventas y fuerza de ventas',
                'description' => Permission::SUBMODULE_PRODUCT_MANAGEMENT
            ],
            [
                'id' => 'e98be444-b0af-3fcf-9e9c-2f2a8e7cc2d7',
                'name' => 'Reporte fecha de vencimiento de productos perecederos',
                'description' => Permission::SUBMODULE_PRODUCT_MANAGEMENT
            ],


            [
                'id' => '1c4e2bf3-85cc-3c91-9bdd-b8cab3c7eed3',
                'name' => 'Información portal de clientes',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT
            ],
            [
                'id' => '2f5f072a-4f0e-30cc-a9d3-cd153716e5d5',
                'name' => 'Reporte de clientes no interesados y carritos abandonados',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT
            ],
            [
                'id' => '6eb30c92-fadf-36f1-b605-d1a3c6772d8c',
                'name' => 'Reporte O/C proceso de pago',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT
            ],
            [
                'id' => '4c0560a5-7498-36ef-85a0-a63f31014b22',
                'name' => 'Reporte de facturas de ventas rechazadas',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT
            ],
            [
                'id' => 'cdc35698-8a88-3c9f-a537-282a7fabe5ad',
                'name' => 'Ventas finalizadas',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT
            ],
            [
                'id' => '4eb2a3dd-9eec-38dd-ae92-8ff1798b9149',
                'name' => 'Reporte de registro de abonos',
                'description' => Permission::SUBMODULE_CUSTOMER_MANAGMENT
            ],


            [
                'id' => '306f519f-8bc3-333e-a732-615739ebdf08',
                'name' => 'Agregar proveedores',
                'description' => Permission::SUBMODULE_SUPPLIER_MANAGEMENT
            ],
            [
                'id' => '07b187fe-040d-3a9b-85cd-8adda9faa3ae',
                'name' => 'Catálogo de proveedores',
                'description' => Permission::SUBMODULE_SUPPLIER_MANAGEMENT
            ],
            [
                'id' => 'f21247d0-df8c-39d1-99ce-fe8031cf858d',
                'name' => 'Compras',
                'description' => Permission::SUBMODULE_SUPPLIER_MANAGEMENT
            ],
            [
                'id' => '46feb680-6f67-3d01-b70d-ae9c3ffb8307',
                'name' => 'Reporte de compra',
                'description' => Permission::SUBMODULE_SUPPLIER_MANAGEMENT
            ],
            [
                'id' => 'c5372c1d-1793-3af9-8fcb-534c4a20cd9e',
                'name' => 'Información histórica: portal de proveedores',
                'description' => Permission::SUBMODULE_SUPPLIER_MANAGEMENT
            ],
        ];

        Permission::query()->insert($permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->truncate();
    }
}
