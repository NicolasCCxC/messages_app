<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('index')->default(0);
        });

        $permissions = collect ([
            //Perfil de la empresa-Información de la empresa
            [
                'id' => 'e0de8b8f-e651-37f8-b3b8-03088d8c9cff',
                'index' => 1
                //Políticas
            ],
            [
                'id' => '0be9509f-14fd-3301-934a-160f26fe85cf',
                'index' => 2
                //Administrador de usuarios
            ],
            [
                'id' => 'c81027c7-585d-384a-a74e-1a26946de073',
                'index' => 3
                //Configuración de notificaciones
            ],
            [
                'id' => '3bec9dee-87a8-3897-b07f-a4f5f7a6db28',
                'index' => 4
                //Centro de notificaciones
            ],
            //Perfil de la empresa-Información de productos y/o servicios
            [
                'id' => '137ab5df-99b2-4198-94cf-bc006e97e7b0',
                'index' => 1
                //Armar bodegas
            ],
            [
                'id' => 'e2c82a27-7cd7-3cf1-a25d-76ecc2bbeb85',
                'index' => 2
                //Información de costo de envíos de productos
            ],
            [
                'id' => 'e3200c57-f704-4010-a590-a73511333940',
                'index' => 3
                //Información de la prestación de servicios
            ],
            [
                'id' => 'b774cda7-7be7-3b10-819f-8692c2ddc97e',
                'index' => 4
                //Listado de catálogo de productos y/o servicios
            ],
            //Reportes analíticos-Gestión de productos/servicios
            [
                'id' => 'dffd2e8b-898a-3568-b77a-10eeb2267ff5',
                'index' => 1
                //Análisis dinámico de productos/servicios
            ],
            [
                'id' => '8c446573-ee3b-3896-96fa-3694c72f7e59',
                'index' => 2
                //Portafolio óptimo de productos/servicios
            ],
            [
                'id' => '9d32fbd3-08d0-33b7-9297-d7794f3b7b80',
                'index' => 3
                //Reporte de rentabilidades
            ],
            [
                'id' => 'fab72e35-7197-339b-872a-7563181084ce',
                'index' => 4
                //Reporte de ventas y fuerza de ventas
            ],
            [
                'id' => 'e98be444-b0af-3fcf-9e9c-2f2a8e7cc2d7',
                'index' => 5
                //Reporte fecha de vencimiento de productos perecederos
            ],
            //Reportes analíticos-Gestión de clientes
            [
                'id' => '2f5f072a-4f0e-30cc-a9d3-cd153716e5d5',
                'index' => 1
                //Reporte de clientes no interesados y carritos abandonados
            ],
            [
                'id' => '6eb30c92-fadf-36f1-b605-d1a3c6772d8c',
                'index' => 2
                //Reporte O/C proceso de pago
            ],
            [
                'id' => '4c0560a5-7498-36ef-85a0-a63f31014b22',
                'index' => 3
                //Reporte de facturas de ventas rechazadas
            ],
            [
                'id' => 'cdc35698-8a88-3c9f-a537-282a7fabe5ad',
                'index' => 4
                //Ventas finalizadas
            ],
            [
                'id' => '4eb2a3dd-9eec-38dd-ae92-8ff1798b9149',
                'index' => 6,
                'name' => 'Reporte de registro abonos: documentos por cobrar'
                //Reporte de registro abonos: documentos por cobrar
            ],
            //Reportes analíticos-Gestión de proveedores
            [
                'id' => '07b187fe-040d-3a9b-85cd-8adda9faa3ae',
                'index' => 1,
                'name' => 'Reporte de notas débito/crédito para compras'
                //Catálogo de proveedores
            ],
            [
                'id' => '306f519f-8bc3-333e-a732-615739ebdf08',
                'index' => 2,
                'name' => 'Registro de abonos: documentos por pagar'
                //Agregar proveedores
            ],
            [
                'id' => 'f21247d0-df8c-39d1-99ce-fe8031cf858d',
                'index' => 3,
                'name' => 'Reporte de registro de abonos: documentos por pagar'
                //Compras
            ],
            [
                'id' => 'c5372c1d-1793-3af9-8fcb-534c4a20cd9e',
                'index' => 4
                //Información histórica: portal de proveedores
            ],
            //Digitalización tienda física - Sistema de administración
            [
                'id' => '7aa53544-0ef8-4f8e-9bbd-df748754cf18',
                'index' => 1
                //Armar base de datos proveedores
            ],
            [
                'id' => 'dfc3a209-993e-402a-a3c4-a6431bce0ca1',
                'index' => 2
                //Catálogo productos/servicios
            ],
            [
                'id' => '9c305a21-e62c-4efc-8575-d8d2d4e2dc72',
                'index' => 3
                //Cotizaciones
            ],
            [
                'id' => '8f5fbaec-2c4e-4306-97b2-8d7020aa653f',
                'index' => 4
                //Conciliaciones
            ],
            [
                'id' => 'a4a36373-e441-4855-a19f-3fd66d123389',
                'index' => 9
                //Herramienta de control de inventarios para los vendedores: POS
            ],
            //Servicios de sitio web y tienda virtual - Diseño página web
            [
                'id' => 'f4d9a3c6-6fbc-30a7-849d-7bc794246f65',
                'index' => 1
                //Información básica
            ],
            [
                'id' => 'ff642d1f-2cf6-3287-9771-e785ea1a89a4',
                'index' => 2
                //Pasarelas de pago
            ],
            [
                'id' => 'ff642d1f-2cf6-3287-9701-e785ea1a89a4',
                'index' => 3
                //Tienda virtual: productos/servicios
            ],
            //Servicios de sitio web y tienda virtual - Administración: notificaciones Diseño
            [
                'id' => 'fe641d1f-2cf9-3287-9701-e785ea1a89a4',
                'index' => 1
                //Notificaciones diarias
            ],
            [
                'id' => 'fe641d1f-2cf9-3287-9701-e785ea1a89a7',
                'index' => 2
                //Verificaciones
            ],
            
            //Facturación electrónica
            [
                'id' => '6cff87f1-9f88-38e1-a27e-953f003f3448',
                'index' => 1,
                'name' => 'Información requerida para la factura electrónica'
                //Parametrización y personalización factura electrónica
            ],
            [
                'id' => '6cff87f1-9f98-38e1-a27e-953f003f1448',
                'index' => 2
                //Crear factura electrónica de venta
            ],
            [
                'id' => 'efb1019c-0136-470b-8c51-8d95ebd6deee',
                'index' => 3
                //Creación de notas débito/notas crédito
            ],
            [
                'id' => '20901b42-e7bb-332e-ba2d-e2bc499eb3b8',
                'index' => 4
                //Registro de abonos
            ],
            [
                'id' => 'fb04e247-2a50-3a9b-b7bf-ef6b3e610d30',
                'index' => 7
                //Documentos electrónicos que requieren acción
            ],
            [
                'id' => 'dfdf5953-b5ed-3354-9238-10fce725fa66',
                'index' => 8
                //Reporte de registro de abonos
            ],
            [
                'id' => 'a9a4cb45-c682-30e0-b038-bd47c2bd406e',
                'index' => 9
                //Reporte de registro de abonos
            ],
            [
                'id' => '91f3c7d2-c8a3-3f00-86db-1e71816bcd4e',
                'index' => 10
                //Si tiene otro proveedor tecnológico: Importar documento XML
            ],
            //Facturación electrónica-Administración: notificaciones Facturación
            [
                'id' => '5f4f2ad9-9e7c-3b73-b6a8-5716d3b96c77',
                'index' => 1
                //Notificaciones diarias
            ],
            //Administración de bodegas-Bodegas
            [
                'id' => '2a8a0e72-1406-3a85-8ff6-a8982f82466b',
                'index' => 2,
                'description' => Permission::SUBMODULE_INVENTORY_CONTROL,
                //Kardex movimiento diario de inventario //Manejo de inventario
            ],
            [
                'id' => '700e42dc-aa22-3276-9aa9-21430a295a91',
                'index' => 6
                //Capacidad máxima de productos por bodegas
            ],
            [
                'id' => 'dec15bc6-4cb0-3f55-9197-b9bf5d49b606',
                'index' => 7
                //Traslado entre bodegas para distribución de inventario
            ],
            [
                'id' => '2e0f3837-6c8f-312a-a779-f09e220fd866',
                'index' => 8
                //Listado traslado entre bodegas
            ],
            [
                'id' => '276bb913-1bf1-3065-ab71-1f1bb6d7e951',
                'index' => 9
                //Nivel mínimo de reabastecimiento por producto por bodega
            ],
            //Administración de bodegas-Manejo de inventario
            [
                'id' => 'd27bd81b-98f5-3b5f-ad53-8c096aa40608',
                'index' => 0,
                'name' => 'Cálculo de costo unitario inicial',
                //Tutorial cálculo de costo unitario inicial
            ],
            [
                'id' => '86ac7739-4a53-4688-a8ef-a9b1c6ff2b8e',
                'index' => 1,
                'name' => 'Cálculo de valor unitario inicial',
                //Tutorial cálculo de valor unitario inicial
            ],
            [
                'id' => '9d1d478f-5f9d-32b1-9a21-7619176b88fa',
                'index' => 2,
                //Control diario de inventario por bodega por producto
            ],
            [
                'id' => '1d539578-631d-35b2-923d-14b3a2b5c02e',
                'index' => 3,
                //Cantidades disponibles por producto por bodega
            ],
            [
                'id' => 'e9e54ec1-41ca-3ecc-9986-8d13a9f7c37d',
                'index' => 4,
                //Control de inventario total por producto por canal de venta
            ],
            [
                'id' => '0d867f46-ce52-3d24-9813-22415e149f38',
                'index' => 5,
                //Control de capacidad máxima de productos por bodegas
            ],
            [
                'id' => '51de4b03-b15e-3043-ae45-34b80bbd22f5',
                'index' => 6,
                //Control de nivel mínimo de reabastecimiento por producto por bodega
            ],
            //Administración de bodegas - Administración: notificaciones Bodegas
            [
                'id' => 'd27bd86b-98f5-3b5f-ad53-8c096aa40608',
                'index' => 1
                //Notificaciones diarias
            ],
             //Planeación y organización
             [
                'id' => '6c97d7a3-a585-4c42-a727-754c4aeb5a77',
                'index' => 1
                //Diagrama de Gantt
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
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('index');
        });
    }
}
