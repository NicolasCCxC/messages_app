<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pay_transactions', function (Blueprint $table) {
            $table->jsonb('json_pse_url_response')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pay_transactions', function (Blueprint $table) {
            $table->dropColumn('json_pse_url_response');
        });
    }
};