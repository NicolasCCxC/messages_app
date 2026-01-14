<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSecurityFiscalResponsibilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_fiscal_responsibilities', function (Blueprint $table) {
            $table->jsonb('withholdings')->default('[{"name":"RETEIVA","is_active":"false"},{"name":"RETEICA","is_active":"false"},{"name":"RETEFUENTE","is_active":"false"}]')->after('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('security_fiscal_responsibilities', function (Blueprint $table) {
            $table->removeColumn('withholdings');
        });
    }
}
