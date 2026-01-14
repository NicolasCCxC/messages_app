<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldDiscountMembershipSubModulesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->string('discount')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
