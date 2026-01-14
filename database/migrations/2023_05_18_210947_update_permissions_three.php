<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePermissionsThree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::query()->find('efb1019c-0136-470b-8c51-8d95ebd6deee')->update([
            'name' => 'Crear nota débito/nota crédito'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::query()->find('efb1019c-0136-470b-8c51-8d95ebd6deee')->update([
            'name' => 'Creación de notas débito/notas crédito'
        ]);
    }
}
