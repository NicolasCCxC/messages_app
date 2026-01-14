<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataBaseForMembershipsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropForeign(['membership_type_id']);
            $table->dropColumn('membership_type_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->integer('pages_available')->default(0);
        });

        Schema::table('pay_transactions', function (Blueprint $table) {
            $table->integer('pages_quantity')->default(0);
        });

        Schema::dropIfExists('membership_types');
        Schema::dropIfExists('group_discounts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('membership_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->double('price_user');
            $table->timestamps();
        });

        Schema::create('group_discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity_modules');
            $table->integer('percentage_discount');
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('pages_available');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->bigInteger('membership_type_id')->unsigned();
        });

        Schema::table('pay_transactions', function (Blueprint $table) {
            $table->dropColumn('pages_quantity');
        });
    }
}
