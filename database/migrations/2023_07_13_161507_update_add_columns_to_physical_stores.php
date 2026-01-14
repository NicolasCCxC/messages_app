<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAddColumnsToPhysicalStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('physical_stores', function (Blueprint $table) {
            $table->integer('country_id')->nullable();
            $table->string('country_name')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('department_name')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city_name')->nullable();
            $table->string('phone', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('physical_stores', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('country_name');
            $table->dropColumn('department_id');
            $table->dropColumn('department_name');
            $table->dropColumn('city_id');
            $table->dropColumn('city_name');
            $table->dropColumn('phone');
        });
    }
}
