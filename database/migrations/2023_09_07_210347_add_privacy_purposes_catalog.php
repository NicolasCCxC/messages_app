<?php

use App\Models\PrivacyPurpose;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPrivacyPurposesCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $privacyPurposes = [
            [
                'id' => '984c1b07-e3fb-4fcf-aa14-e79dd1d72884',
                'description' => "Ejercer  su  derecho  de  conocer  de  manera  suficiente  al  Titular  con  quien  se  propone  entablar relaciones,  prestar  servicios  y  valorar  el  riesgo  presente  o  futuro  de  las  mismas  relaciones  y servicios.",
                'is_default' => true
            ],
            [
                'id' => '913f7590-f5df-4f97-a216-402c5c23f11e',
                'description' => "Efectuar las gestiones pertinentes para el desarrollo de la etapa precontractual, contractual y post contractual con la Compañía.",
                'is_default' => true
            ],
            [
                'id' => '2a22d944-2220-4130-8a13-1c3e62f4f99c',
                'description' => "Validar y verificar la identidad del cliente para el ofrecimiento de productos y servicios, así mismo para compartir la información con diversos actores del mercado.",
                'is_default' => true
            ],
            [
                'id' => '60e2d0ba-8308-44e1-8b58-0e95c77273b1',
                'description' => "Enviar  información  de  campañas  comerciales  actuales  y  futuras,  promoción  de  productos  y servicios tanto propios como de terceros.",
                'is_default' => true
            ],
            [
                'id' => '521ca358-217e-451b-8943-72134c2a94d1',
                'description' => "Ofrecer  y  prestar  productos  o  servicios  a  través  de  cualquier  medio  o  canal  de  acuerdo  con  el perfil del cliente y los avances tecnológicos.",
                'is_default' => true
            ],
            [
                'id' => 'b2b86a8a-dfda-4b0d-8709-56134a3ca1a9',
                'description' => "Suministrar información comercial, legal, de productos, de seguridad, de servicio o de cualquier otra índole.",
                'is_default' => true
            ],
            [
                'id' => 'c9fb56bc-da1e-4490-a2f6-8dc35139f8e5',
                'description' => "Recibir,  verificar,  evaluar  y  filtrar  las  solicitudes  de  créditos  a  través  de  un  análisis  de  riesgo crediticio y viabilidad financiera.",
                'is_default' => true
            ],
            [
                'id' => 'fad577f3-9e5a-47a8-b912-8cc1f6c0aafd',
                'description' => "Responder a solicitudes de autoridades judiciales o administrativas competentes, procurando en todo caso divulgar sólo la información pertinente y necesaria para dar respuesta a la respectiva solicitud.",
                'is_default' => true
            ],
            [
                'id' => '3192f6aa-e642-464c-8c35-6de566796dbe',
                'description' => "Guardar la información suministrada en las bases de datos de la Compañía.",
                'is_default' => true
            ],
            [
                'id' => '1d3f8b55-20b6-4a56-91df-d5bfe94d1370',
                'description' => "Hacer seguimiento y gestionar los cobros y colocaciones de recursos.",
                'is_default' => true
            ],
            [
                'id' => '9868643f-3960-4ea9-8213-ea538bf6fe03',
                'description' => "Contactar  al  Titular  de  los  Datos  Personales  por  diferentes  medios  para  realizar  encuestas  de satisfacción respecto de los productos y servicios ofrecidos por la Compañía.",
                'is_default' => true
            ],
            [
                'id' => 'b44d656a-02cd-4a34-9b8e-b45aa85fc6d0',
                'description' => "Realizar estudios de mercado, acciones de inteligencia de negocios, investigaciones de tendencias de mercado.",
                'is_default' => true
            ],
            [
                'id' => '0eb61e47-37ab-47d4-a51b-14b054ad6506',
                'description' => "Atender las solicitudes relacionadas con ventas, estado de orden de compra y servicio al cliente.",
                'is_default' => true
            ],
            [
                'id' => 'fccc5e80-094a-467f-9bbf-bafd8967b8be',
                'description' => "Gestionar  las  consultas,  quejas  y  reclamos  presentadas  por  los  Titulares  de  Datos  Personales, respecto de los servicios y productos ofrecidos por la Compañía.",
                'is_default' => true
            ],
            [
                'id' => 'bb77cea6-4ac2-47bb-84fb-8b4a22dfc12c',
                'description' => "Gestionar transacciones electrónicas con pasarelas de pago.",
                'is_default' => true
            ],
            [
                'id' => '9a87ec8b-d7af-45e8-a7c6-f1c3457d0503',
                'description' => "Realizar  el  proceso  de  selección  y  vinculación  del  proveedor  o  contratista  con  la  Compañía, generando el desarrollo de los procedimientos internos.",
                'is_default' => true
            ],
            [
                'id' => 'fbd56d3b-adae-4390-820f-a932c53b88ae',
                'description' => "Realizar todas las gestiones de orden tributario, contable, fiscal y de facturación.",
                'is_default' => true
            ],
        ];
        PrivacyPurpose::query()->insert($privacyPurposes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('privacy_purposes')->truncate();
    }
}
