<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipFields extends Migration
{
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->string('payment_status')->nullable();
            $table->string('payment_method')->nullable();
        });
    }

    public function down()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_method');
        });
    }
}
