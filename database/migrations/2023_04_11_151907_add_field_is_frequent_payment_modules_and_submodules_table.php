<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIsFrequentPaymentModulesAndSubmodulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->boolean('is_frequent_payment')->default(false);
        });

        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->boolean('is_frequent_payment')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->dropColumn('is_frequent_payment');
        });

        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->dropColumn('is_frequent_payment');
        });
    }
}
