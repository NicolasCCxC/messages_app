<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipHasModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_has_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('membership_id');
            $table->boolean('is_active')->default(true);
            $table->foreign('membership_id')->references('id')->on('memberships');
            $table->unsignedBigInteger('membership_modules_id');
            $table->index('membership_modules_id','idx_membership_modules_id_modules_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('membership_has_modules');
    }
}
